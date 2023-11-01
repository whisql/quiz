<?php

namespace App\Entity;

use App\Enum\TaskDecisionType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Decision::class, orphanRemoval: true)]
    private Collection $decisions;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: CorrectDecision::class, orphanRemoval: true)]
    private Collection $correctDecisions;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Questionnaire $questionnaire = null;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(enumType: TaskDecisionType::class)]
    private TaskDecisionType $decisionType;

    #[ORM\Column(type: 'integer')]
    private int $priority = 0;

    public function __construct()
    {
        $this->decisions        = new ArrayCollection();
        $this->correctDecisions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Decision>
     */
    public function getDecisions(): Collection
    {
        return $this->decisions;
    }

    public function addDecision(Decision $decision): static
    {
        if (!$this->decisions->contains($decision)) {
            $this->decisions->add($decision);
            $decision->setTask($this);
        }

        return $this;
    }

    public function removeDecision(Decision $decision): static
    {
        if ($this->decisions->removeElement($decision)) {
            // set the owning side to null (unless already changed)
            if ($decision->getTask() === $this) {
                $decision->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CorrectDecision>
     */
    public function getCorrectDecisions(): Collection
    {
        return $this->correctDecisions;
    }

    public function addCorrectDecision(CorrectDecision $correctDecision): static
    {
        if (!$this->correctDecisions->contains($correctDecision)) {
            $this->correctDecisions->add($correctDecision);
            $correctDecision->setTask($this);
        }

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): static
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDecisionType(): TaskDecisionType
    {
        return $this->decisionType;
    }

    public function setDecisionType(TaskDecisionType $decisionType): void
    {
        $this->decisionType = $decisionType;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
