<?php

namespace App\Entity;

use App\Repository\CorrectDecisionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorrectDecisionRepository::class)]
class CorrectDecision
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'correctDecisions')]
    #[ORM\JoinColumn(nullable: false)]
    private Task $task;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Decision $decision = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $inputValue;

    #[ORM\Column(type: 'integer')]
    private int $cost = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): static
    {
        $this->task = $task;

        return $this;
    }

    public function getDecision(): ?Decision
    {
        return $this->decision;
    }

    public function setDecision(?Decision $decision): static
    {
        $this->decision = $decision;

        return $this;
    }

    public function getInputValue(): ?string
    {
        return $this->inputValue;
    }

    public function setInputValue(?string $inputValue): static
    {
        $this->inputValue = $inputValue;

        return $this;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }
}
