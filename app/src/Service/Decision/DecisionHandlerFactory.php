<?php
declare(strict_types=1);


namespace App\Service\Decision;


use App\Service\Decision\Command\DecisionCommandInterface;
use App\Service\Decision\Handler\DecisionHandlerInterface;

class DecisionHandlerFactory
{
    /**
     * @var DecisionHandlerInterface[]
     */
    private array $decisionHandlers = [];

    /**
     * @var DecisionHandlerInterface[] $decisionHandlers
     */
    public function __construct(iterable $decisionHandlers)
    {
        foreach ($decisionHandlers as $decisionHandler) {
            if (isset($this->decisionHandlers[$decisionHandler->getDecisionCommand()])) {
                throw new \LogicException('Few decision resolvers for one decision command');
            }

            $this->decisionHandlers[$decisionHandler->getDecisionCommand()] = $decisionHandler;
        }
    }

    public function getDecisionHandler(DecisionCommandInterface $command): DecisionHandlerInterface
    {
        $commandClass = $command::class;

        if (!isset($this->decisionHandlers[$commandClass])) {
            throw new \InvalidArgumentException(
                "Decision handler does not exist for decision command '{$commandClass}'"
            );
        }

        return $this->decisionHandlers[$commandClass];
    }
}