<?php

namespace App\Entity;

use App\Repository\ProjectDecisionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectDecisionRepository::class)]
class ProjectDecision
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Decision $decision;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $inputValue = null;

    #[ORM\ManyToOne(inversedBy: 'projectDecisions')]
    #[ORM\JoinColumn(nullable: false)]
    private ProjectTask $projectTask;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getProjectTask(): ProjectTask
    {
        return $this->projectTask;
    }

    public function setProjectTask(ProjectTask $projectTask): static
    {
        $this->projectTask = $projectTask;

        return $this;
    }
}
