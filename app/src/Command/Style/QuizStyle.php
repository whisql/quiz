<?php
declare(strict_types=1);


namespace App\Command\Style;


use App\Dto\AnswerOutput;
use App\Dto\ProjectResultOutput;
use App\Entity\Questionnaire;
use App\Entity\Task;
use App\Enum\ProjectTaskOrderStrategy;
use App\Enum\TaskDecisionType;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class QuizStyle extends SymfonyStyle
{
    private string $messageChoiceQuestionnaire = 'Выберите опросник';
    private string $messageChoiceStrategy      = 'Выберите стратегию порядка вопросов';
    private string $messageChoiceSingleSelect  = 'Выберите один вариант (номер ответа)';
    private string $messageChoiceMultiSelect   = 'Выберите один или несколько вариантов (номера ответов через запятую)';
    private string $messageInputText           = 'Введите ответ';
    private string $messageDuplicateError      = 'В ответе не должно быть одинаковых вариантов';

    public function choiceQuestionnaire(array $questionnaires): Questionnaire
    {
        $choices            = $this->getChoices($questionnaires, 'getName');
        $choice             = $this->choice($this->messageChoiceQuestionnaire, $choices);
        $questionnaireIndex = array_flip($choices)[$choice];

        return $questionnaires[$questionnaireIndex];
    }

    public function choiceOrderStrategy(): ProjectTaskOrderStrategy
    {
        $strategy = $this->choice($this->messageChoiceStrategy, [
            ProjectTaskOrderStrategy::DIRECT->value   => '1 По ID',
            ProjectTaskOrderStrategy::SHUFFLE->value  => '2 Случайный',
            ProjectTaskOrderStrategy::PRIORITY->value => '3 По приоритету',
        ]);

        return ProjectTaskOrderStrategy::from($strategy);
    }

    public function choiceByTask(Task $task): mixed
    {
        $decisionType = $task->getDecisionType();
        $this->printDecisionType($decisionType);
        $decisions = $task->getDecisions()->toArray();

        if (empty($decisions)) {
            return $this->ask($task->getDescription());
        }

        $choices        = $this->getChoices($decisions, 'getTitle');
        $questionChoice = new ChoiceQuestion(
            $task->getDescription(),
            $choices
        );

        $questionChoice->setNormalizer(fn($value) => $value ?? '');

        switch ($decisionType) {
            case TaskDecisionType::SINGLE_SELECT:
                $this->setQuestionAsSingleSelect($questionChoice, $choices);
                break;
            case TaskDecisionType::MULTI_SELECT:
                $this->setQuestionAsMultiSelect($questionChoice, $choices);
                break;
            case TaskDecisionType::INPUT_TEXT:
                return $this->ask($task->getDescription());
        }

        return $this->askQuestion($questionChoice);
    }

    public function tableQuestionnaireResult(ProjectResultOutput $resultOutput): void
    {
        $headers = [
            'Вопрос',
            'Ваш ответ',
            'Засчитан?',
            'Количество заработанных очков',
            'Количество максимальных очков за вопрос',
        ];
        $rows    = [];
        foreach ($resultOutput->getProjectTaskResults() as $projectTaskResult) {
            $correctMark = $projectTaskResult->isCorrect() ? '<info>+</info>' : '<error>-</error>';
            $rows[]      = [
                $projectTaskResult->getTitle(),
                implode(', ', array_map(static fn(AnswerOutput $answerOutput
                ) => $answerOutput->getTitle(), $projectTaskResult->getAnswers())),
                $correctMark,
                $projectTaskResult->getSummaryCost(),
                $projectTaskResult->getTotalCorrectDecisionCost(),
            ];
        }
        $footer = sprintf(
            'Количество заработанных очков: %s из %s',
            $resultOutput->getSummaryCost(),
            $resultOutput->getTotalCost());

        $this->createTable()
            ->setHeaderTitle(sprintf('Результаты опроса "%s"', $resultOutput->getQuestionnaireName()))
            ->setHeaders($headers)
            ->setRows($rows)
            ->setFooterTitle($footer)
            ->render();
    }

    private function getChoices(array $array, string $function): array
    {
        $choices = [];
        foreach ($array as $item) {
            $choices[] = sprintf("<info>*</info> %s", $item->$function());
        }

        return $choices;
    }

    private function setQuestionAsMultiSelect(Question $question, array $choices): void
    {
        $question->setMultiselect(true);
        $question->setValidator(function (string $answer) use ($choices): array {
            $seen = [];

            $answers = explode(',', $answer);
            foreach ($answers as $answerItem) {
                $answerItem = trim($answerItem);

                if (in_array($answerItem, $seen)) {
                    throw new \RuntimeException($this->messageDuplicateError);
                }
                $seen[] = $answerItem;

                if (false === array_key_exists($answerItem, $choices)) {
                    throw new \RuntimeException($this->messageChoiceMultiSelect);
                }
            }

            return $seen;
        });
    }

    private function setQuestionAsSingleSelect(Question $question, array $choices): void
    {
        $question->setMultiselect(false);
        $question->setValidator(function (string $answer) use ($choices): string {
            if (false === array_key_exists($answer, $choices)) {
                throw new \RuntimeException($this->messageChoiceSingleSelect);
            }

            return $answer;
        });
    }


    private function printDecisionType(TaskDecisionType $taskDecisionType): void
    {
        $this->write(sprintf('<info>%s</info>', match ($taskDecisionType) {
                TaskDecisionType::SINGLE_SELECT => $this->messageChoiceSingleSelect,
                TaskDecisionType::MULTI_SELECT => $this->messageChoiceMultiSelect,
                TaskDecisionType::INPUT_TEXT => $this->messageInputText
            })
        );
    }

}