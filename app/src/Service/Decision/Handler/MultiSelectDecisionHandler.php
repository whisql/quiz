<?php
declare(strict_types=1);


namespace App\Service\Decision\Handler;


use App\Service\Decision\Command\MultiSelectDecisionCommand;

/**
 * @property MultiSelectDecisionCommand $command
 */
class MultiSelectDecisionHandler extends AbstractDecisionHandler
{
    public function executeCommand(): void
    {
        foreach ($this->command->getDecisions() as $decision) {
            $this->addProjectDecision($decision);
        }
    }

    public function getDecisionCommand(): string
    {
        return MultiSelectDecisionCommand::class;
    }
}