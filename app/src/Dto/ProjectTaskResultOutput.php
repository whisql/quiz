<?php
declare(strict_types=1);


namespace App\Dto;


use App\Entity\CorrectDecision;
use App\Entity\ProjectTask;
use App\Enum\TaskDecisionType;

readonly class ProjectTaskResultOutput
{
    public function __construct(private ProjectTask $projectTask, private array $answers)
    {
    }

    public function getTitle(): string
    {
        return $this->projectTask->getTask()->getDescription();
    }

    public function getMinTitle(): string
    {
        return substr($this->getTitle(), 0, 16).'...';
    }

    /**
     * @return AnswerOutput[]
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function isCorrect(): bool
    {
        $correctAnswers = array_map(static fn(
            CorrectDecision $correctDecision
        ) => $correctDecision->getDecision() ?
            $correctDecision->getDecision()->getTitle() : $correctDecision->getInputValue(),
            $this->projectTask->getTask()->getCorrectDecisions()->getValues()
        );

        $correct = true;
        foreach ($this->getAnswers() as $answer) {
            if (!in_array($answer->getTitle(), $correctAnswers)) {
                $correct = false;
            }
        }

        return $correct;
    }

    public function getSummaryCost(): int
    {
        $cost = 0;

        foreach ($this->getAnswers() as $answer) {
            if (!$answer->isCorrect()) {
                return 0;
            }
            $cost += $answer->getCost();
        }

        return $cost;
    }

    public function getTotalCorrectDecisionCost(): int
    {
        $cost             = 0;
        $decisionType     = $this->projectTask->getTask()->getDecisionType();
        $correctDecisions = $this->projectTask->getTask()->getCorrectDecisions();

        if ($decisionType === TaskDecisionType::SINGLE_SELECT) {
            return $correctDecisions->first()->getCost();
        }

        if ($decisionType === TaskDecisionType::INPUT_TEXT) {
            $correctCosts = array_map(static fn(CorrectDecision $correctDecision
            ) => $correctDecision->getCost(), $correctDecisions->getValues());

            return max($correctCosts);
        }


        foreach ($correctDecisions as $correctDecision) {
            $cost += $correctDecision->getCost();
        }

        return $cost;
    }

    public function getProjectTask(): ProjectTask
    {
        return $this->projectTask;
    }

}