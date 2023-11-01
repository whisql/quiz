<?php
declare(strict_types=1);


namespace App\Service\Decision\Command;


readonly class InputTextDecisionCommand implements DecisionCommandInterface
{
    public function __construct(private string $inputValue)
    {
    }

    public function getInputValue(): string
    {
        return $this->inputValue;
    }

}