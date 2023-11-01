<?php
declare(strict_types=1);


namespace App\Service\Decision\Handler;

use App\Service\Decision\Command\SingleSelectDecisionCommand;

/**
 * @property SingleSelectDecisionCommand $command
 */
class SingleSelectDecisionHandler extends AbstractDecisionHandler
{
    public function executeCommand(): void
    {
        $this->addProjectDecision($this->command->getDecision());
    }

    public function getDecisionCommand(): string
    {
        return SingleSelectDecisionCommand::class;
    }
}