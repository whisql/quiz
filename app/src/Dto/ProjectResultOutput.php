<?php
declare(strict_types=1);


namespace App\Dto;


use App\Entity\Project;

readonly class ProjectResultOutput
{
    public function __construct(
        private Project $project,
        private array $projectTaskResults
    ) {
    }

    /**
     * @return ProjectTaskResultOutput[]
     */
    public function getProjectTaskResults(): array
    {
        return $this->projectTaskResults;
    }

    public function getQuestionnaireName(): string
    {
        return $this->project->getQuestionnaire()->getName();
    }

    public function getSummaryCost(): int
    {
        $sum = 0;
        foreach ($this->getProjectTaskResults() as $projectTaskResult) {
            $sum += $projectTaskResult->getSummaryCost();
        }

        return $sum;
    }

    public function getTotalCost(): int
    {
        $total = 0;
        foreach ($this->getProjectTaskResults() as $projectTaskResult) {
            $total += $projectTaskResult->getTotalCorrectDecisionCost();
        }

        return $total;
    }


}