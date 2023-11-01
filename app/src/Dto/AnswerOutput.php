<?php
declare(strict_types=1);


namespace App\Dto;


readonly class AnswerOutput
{
    public function __construct(
        private string $title,
        private bool $correct,
        private int $cost
    ) {

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isCorrect(): bool
    {
        return $this->correct;
    }

    public function getCost(): int
    {
        return $this->cost;
    }
}