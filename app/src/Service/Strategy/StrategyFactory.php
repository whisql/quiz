<?php
declare(strict_types=1);


namespace App\Service\Strategy;


use App\Enum\ProjectTaskOrderStrategy;

class StrategyFactory
{
    private iterable $strategies;

    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            if (!$strategy instanceof TaskCreationStrategyInterface) {
                throw new \LogicException('Strategy must be instance of TaskCreationStrategyInterface');
            }

            if (isset($this->strategies[$strategy->getStrategyName()->value])) {
                throw new \LogicException('Few strategy for one strategy name');
            }

            $this->strategies[$strategy->getStrategyName()->value] = $strategy;
        }
    }

    public function getStrategy(ProjectTaskOrderStrategy $taskOrderStrategy): TaskCreationStrategyInterface
    {
        if (!isset($this->strategies[$taskOrderStrategy->value])) {
            throw new \InvalidArgumentException(sprintf(
                "Strategy does not exist for strategy name '%s'",
                $taskOrderStrategy->value
            ));
        }

        return $this->strategies[$taskOrderStrategy->value];
    }
}