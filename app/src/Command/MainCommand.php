<?php
declare(strict_types=1);


namespace App\Command;


use App\Command\Style\QuizStyle;
use App\Entity\Project;
use App\Entity\ProjectTask;
use App\Enum\TaskDecisionType;
use App\Repository\ProjectTaskRepository;
use App\Repository\QuestionnaireRepository;
use App\Service\Decision\Command\InputTextDecisionCommand;
use App\Service\Decision\Command\MultiSelectDecisionCommand;
use App\Service\Decision\Command\SingleSelectDecisionCommand;
use App\Service\ProjectManager;
use App\Service\ProjectTaskManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'quiz:start')]
class MainCommand extends Command
{
    private QuizStyle $io;

    public function __construct(
        private readonly ProjectManager $projectManager,
        private readonly ProjectTaskRepository $projectTaskRepository,
        private readonly QuestionnaireRepository $questionnaireRepository,
        private readonly ProjectTaskManager $taskManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io       = new QuizStyle($input, $output);
        $questionnaires = $this->questionnaireRepository->findAll();

        if (empty($questionnaires)) {
            $this->io->error('Не найдено ни одного опросника');

            return Command::FAILURE;
        }

        $questionnaire = $this->io->choiceQuestionnaire($questionnaires);
        $strategy      = $this->io->choiceOrderStrategy();

        $project = new Project($questionnaire, $strategy);

        $this->projectManager->init($project);

        while ($projectTask = $this->projectTaskRepository->findLatestActiveTaskByProject($project)) {
            $decisionType = $projectTask->getTask()->getDecisionType();

            match ($decisionType) {
                TaskDecisionType::MULTI_SELECT => $this->decideMultiSelect($projectTask),
                TaskDecisionType::SINGLE_SELECT => $this->decideSingleSelect($projectTask),
                TaskDecisionType::INPUT_TEXT => $this->decideInputText($projectTask)
            };
        }

        $result = $this->projectManager->getQuestionnaireResult($project);
        $this->io->tableQuestionnaireResult($result);

        return Command::SUCCESS;
    }


    private function decideMultiSelect(ProjectTask $projectTask): void
    {
        $decisions = $projectTask->getTask()->getDecisions()->toArray();
        $choices   = $this->io->choiceByTask($projectTask->getTask());

        $choicesDecisions = [];

        foreach ($choices as $choice) {
            $choicesDecisions[] = $decisions[$choice];
        }

        $this->taskManager->decideProjectTask(
            $projectTask,
            new MultiSelectDecisionCommand($choicesDecisions)
        );
    }

    private function decideSingleSelect(ProjectTask $projectTask): void
    {
        $decisions = $projectTask->getTask()->getDecisions()->toArray();
        $choice    = $this->io->choiceByTask($projectTask->getTask());

        $decision = $decisions[$choice];

        $this->taskManager->decideProjectTask($projectTask, new SingleSelectDecisionCommand($decision));
    }

    private function decideInputText(ProjectTask $projectTask): void
    {
        $choice = $this->io->choiceByTask($projectTask->getTask());

        $this->taskManager->decideProjectTask($projectTask, new InputTextDecisionCommand($choice));
    }
}