<?php
declare(strict_types=1);


namespace App\Service\Strategy;


use App\Entity\Project;
use App\Entity\ProjectTask;
use App\Enum\ProjectTaskOrderStrategy;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ShuffleOrderStrategy implements TaskCreationStrategyInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TaskRepository $taskRepository
    ) {
    }

    public function getStrategyName(): ProjectTaskOrderStrategy
    {
        return ProjectTaskOrderStrategy::SHUFFLE;
    }

    public function createProjectTasks(Project $project): void
    {
        foreach ($this->taskRepository->findRandomTasksByQuestionnaire($project->getQuestionnaire()) as $task) {
            $projectTask = new ProjectTask($project);
            $projectTask->setTask($task);

            $this->em->persist($projectTask);
        }
        $this->em->flush();
    }
}