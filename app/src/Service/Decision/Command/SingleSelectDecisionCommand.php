<?php
declare(strict_types=1);


namespace App\Service\Decision\Command;


use App\Entity\Decision;

readonly class SingleSelectDecisionCommand implements DecisionCommandInterface
{
    public function __construct(private Decision $decision)
    {
    }

    public function getDecision(): Decision
    {
        return $this->decision;
    }

}