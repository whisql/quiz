<?php
declare(strict_types=1);


namespace App\Service;

use App\Entity\ProjectTask;
use App\Service\Decision\Command\DecisionCommandInterface;
use App\Service\Decision\DecisionHandlerFactory;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProjectTaskManager
{
    public function __construct(
        private DecisionHandlerFactory $decisionHandlerFactory,
        private ProjectManager $projectManager,
        private EntityManagerInterface $em,
        private Workflow $workflow
    ) {
    }

    public function decideProjectTask(ProjectTask $projectTask, DecisionCommandInterface $decisionCommand): void
    {
        try {
            $this->em->beginTransaction();
            $this->decisionHandlerFactory->getDecisionHandler($decisionCommand)->resolve($projectTask, $decisionCommand);

            $projectTask->makeCompletedCondition();
            $this->em->flush();

            $this->workflow->activateNextProjectTask($projectTask->getProject());

            $this->projectManager->tryToCompleteProject($projectTask->getProject());
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            throw $exception;
        }
    }
}