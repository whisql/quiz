<?php
declare(strict_types=1);


namespace App\Service\Strategy;


use App\Entity\Project;
use App\Entity\ProjectTask;
use App\Enum\ProjectTaskOrderStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


final readonly class DirectOrderStrategy implements TaskCreationStrategyInterface
{
    private EntityManagerInterface $em;

    #[Required]
    public function setEntityManagerInterface(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    public function getStrategyName(): ProjectTaskOrderStrategy
    {
        return ProjectTaskOrderStrategy::DIRECT;
    }

    public function createProjectTasks(Project $project): void
    {
        $questionnaire = $project->getQuestionnaire();

        foreach ($questionnaire->getTasks() as $task) {
            $projectTask = new ProjectTask($project);
            $projectTask->setTask($task);

            $this->em->persist($projectTask);
        }

        $this->em->flush();
    }

}