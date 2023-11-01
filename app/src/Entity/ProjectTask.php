<?php

namespace App\Entity;

use App\Enum\ProjectTaskCondition;
use App\Repository\ProjectTaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectTaskRepository::class)]
class ProjectTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Project $project;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $completedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $activatedAt = null;

    #[ORM\Column(enumType: ProjectTaskCondition::class)]
    private ProjectTaskCondition $condition = ProjectTaskCondition::PENDING;

    #[ORM\OneToMany(mappedBy: 'projectTask', targetEntity: ProjectDecision::class, orphanRemoval: true)]
    private Collection $projectDecisions;

    public function __construct(Project $project)
    {
        $this->project          = $project;
        $this->projectDecisions = new ArrayCollection();
        $this->createdAt        = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function makeActiveCondition(): static
    {
        $this->condition   = ProjectTaskCondition::ACTIVE;
        $this->activatedAt = new \DateTime();

        return $this;
    }

    public function makeCompletedCondition(): static
    {
        $this->condition   = ProjectTaskCondition::COMPLETED;
        $this->completedAt = new \DateTime();

        return $this;
    }


    public function getCondition(): ProjectTaskCondition
    {
        return $this->condition;
    }

    public function getActivatedAt(): ?\DateTime
    {
        return $this->activatedAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function isConditionCompleted(): bool
    {
        return $this->condition === ProjectTaskCondition::COMPLETED;
    }

    /**
     * @return Collection<int, ProjectDecision>
     */
    public function getProjectDecisions(): Collection
    {
        return $this->projectDecisions;
    }

    public function addProjectDecision(ProjectDecision $projectDecision): static
    {
        if (!$this->projectDecisions->contains($projectDecision)) {
            $this->projectDecisions->add($projectDecision);
            $projectDecision->setProjectTask($this);
        }

        return $this;
    }

    public function removeProjectDecision(ProjectDecision $projectDecision): static
    {
        if ($this->projectDecisions->removeElement($projectDecision)) {
            // set the owning side to null (unless already changed)
            if ($projectDecision->getProjectTask() === $this) {
                $projectDecision->setProjectTask(null);
            }
        }

        return $this;
    }
}
