<?php
declare(strict_types=1);


namespace App\Service;


use App\Entity\Project;
use App\Repository\ProjectTaskRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class Workflow
{
    public function __construct(
        private ProjectTaskRepository $projectTaskRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function activateNextProjectTask(Project $project): void
    {
        if (!$projectTask = $this->projectTaskRepository->findLatestPendingTaskByProject($project)) {
            return;
        }

        $projectTask->makeActiveCondition();
        $this->em->flush();
    }
}