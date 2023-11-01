<?php
declare(strict_types=1);


namespace App\Service\Decision\Handler;


use App\Service\Decision\Command\InputTextDecisionCommand;

/**
 * @property InputTextDecisionCommand $command
 */
class InputTextDecisionHandler extends AbstractDecisionHandler
{
    public function executeCommand(): void
    {
        $this->addProjectDecision(inputValue: $this->command->getInputValue());
    }

    public function getDecisionCommand(): string
    {
        return InputTextDecisionCommand::class;
    }
}