<?php
declare(strict_types=1);


namespace App\Service\Decision\Handler;


use App\Entity\Decision;
use App\Entity\ProjectTask;
use App\Entity\ProjectDecision;
use App\Service\Decision\Command\DecisionCommandInterface;
use App\Service\Exception\AlreadyCompletedProjectTaskException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


abstract class AbstractDecisionHandler implements DecisionHandlerInterface
{
    protected ProjectTask              $projectTask;
    protected EntityManagerInterface   $em;
    protected DecisionCommandInterface $command;

    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    abstract public function executeCommand();

    /**
     * @throws AlreadyCompletedProjectTaskException
     */
    public function resolve(ProjectTask $projectTask, DecisionCommandInterface $command): void
    {
        if ($projectTask->isConditionCompleted()) {
            throw new AlreadyCompletedProjectTaskException("ProjectTask #{$projectTask->getId()} already completed. Cannot decide it again.");
        }

        $this->projectTask = $projectTask;
        $this->command     = $command;

        $this->executeCommand();
    }

    protected function addProjectDecision(
        ?Decision $decision = null,
        ?string $inputValue = null
    ): void {
        if (null !== $decision && !$this->projectTask->getTask()->getDecisions()->contains($decision)) {
            throw new \LogicException(sprintf(
                'ProjectTask #%s does not contain decision #%s',
                $this->projectTask->getTask()->getId(),
                $decision?->getId()
            ));
        }

        $projectDecision = (new ProjectDecision())
            ->setDecision($decision)
            ->setInputValue($inputValue);

        $this->em->persist($projectDecision);
        $this->projectTask->addProjectDecision($projectDecision);
    }
}