<?php
declare(strict_types=1);


namespace App\Service\Strategy;


use App\Entity\Project;
use App\Enum\ProjectTaskOrderStrategy;

interface TaskCreationStrategyInterface
{
    public function getStrategyName(): ProjectTaskOrderStrategy;

    public function createProjectTasks(Project $project): void;
}