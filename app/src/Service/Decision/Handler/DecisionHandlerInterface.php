<?php
declare(strict_types=1);


namespace App\Service\Decision\Handler;


use App\Entity\ProjectTask;
use App\Service\Decision\Command\DecisionCommandInterface;

interface DecisionHandlerInterface
{
    public function getDecisionCommand(): string;

    public function resolve(ProjectTask $projectTask, DecisionCommandInterface $command): void;
}