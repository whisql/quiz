<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\AnswerOutput;
use App\Dto\ProjectResultOutput;
use App\Dto\ProjectTaskResultOutput;
use App\Entity\Project;
use App\Repository\ProjectTaskRepository;
use App\Service\Strategy\StrategyFactory;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProjectManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private StrategyFactory $strategyAnalyzer,
        private ProjectTaskRepository $projectTaskRepository,
        private Workflow $workflow
    ) {
    }

    public function init(Project $project): void
    {
        try {
            $this->em->beginTransaction();
            $this->em->persist($project);
            $this->em->flush();

            $strategy = $this->strategyAnalyzer->getStrategy($project->getTaskOrderStrategy());
            $strategy->createProjectTasks($project);

            $this->workflow->activateNextProjectTask($project);

            $this->tryToCompleteProject($project);

            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $throwable) {
            $this->em->rollback();
            throw  $throwable;
        }
    }

    public function tryToCompleteProject(Project $project): void
    {
        if ($this->projectTaskRepository->findLatestPendingTaskByProject($project)) {
            return;
        }

        $project->makeStatusCompleted();
        $this->em->flush();
    }

    public function getQuestionnaireResult(Project $project): ProjectResultOutput
    {
        if (!$project->isConditionCompleted()) {
            throw new \LogicException("Project#{$project->getId()} is not completed");
        }

        $projectTasks = $this->projectTaskRepository->findAllCompletedByProject($project);

        $dto = [];
        foreach ($projectTasks as $projectTask) {
            $answers          = [];
            $correctDecisions = $projectTask->getTask()->getCorrectDecisions();
            $correctAnswers   = [];

            foreach ($correctDecisions as $correctDecision) {
                $correctAnswer                  = $correctDecision->getDecision() ? $correctDecision->getDecision()->getTitle() : $correctDecision->getInputValue();
                $correctAnswers[$correctAnswer] = $correctDecision;
            }

            foreach ($projectTask->getProjectDecisions() as $projectDecision) {
                $answer  = $projectDecision->getDecision() ? $projectDecision->getDecision()->getTitle() : $projectDecision->getInputValue();
                $cost    = 0;
                $correct = false;
                if (array_key_exists($answer, $correctAnswers)) {
                    $cost    += $correctAnswers[$answer]->getCost();
                    $correct = true;
                }
                $answers[] = new AnswerOutput($answer, $correct, $cost);
            }
            $dto[] = new ProjectTaskResultOutput($projectTask, $answers);
        }

        return new ProjectResultOutput($project, $dto);
    }
}