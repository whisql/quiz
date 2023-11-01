<?php

namespace App\Entity;

use App\Enum\ProjectCondition;
use App\Enum\ProjectTaskOrderStrategy;
use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Questionnaire $questionnaire;

    #[ORM\Column(enumType: ProjectCondition::class)]
    private ProjectCondition $condition = ProjectCondition::ACTIVE;

    #[ORM\Column]
    private \DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $completedAt = null;

    #[ORM\Column(enumType: ProjectTaskOrderStrategy::class)]
    private ProjectTaskOrderStrategy $taskOrderStrategy;

    public function __construct(Questionnaire $questionnaire, ProjectTaskOrderStrategy $taskOrderStrategy)
    {
        $this->questionnaire     = $questionnaire;
        $this->taskOrderStrategy = $taskOrderStrategy;
        $this->createdAt         = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionnaire(): Questionnaire
    {
        return $this->questionnaire;
    }

    public function getCondition(): ProjectCondition
    {
        return $this->condition;
    }

    public function makeStatusCompleted(): static
    {
        $this->condition   = ProjectCondition::COMPLETED;
        $this->completedAt = new \DateTime();

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function getTaskOrderStrategy(): ProjectTaskOrderStrategy
    {
        return $this->taskOrderStrategy;
    }

    public function isConditionCompleted(): bool
    {
        return $this->condition === ProjectCondition::COMPLETED;
    }
}
