<?php
declare(strict_types=1);


namespace App\Service\Decision\Command;

use App\Entity\Decision;

readonly class MultiSelectDecisionCommand implements DecisionCommandInterface
{
    /**
     * @var Decision[]
     */
    private array $decisions;

    public function __construct(array $decisions)
    {
        foreach ($decisions as $decision) {
            if (!$decision instanceof Decision) {
                throw new \InvalidArgumentException("Expected Decisions[] array as argument");
            }
        }

        $this->decisions = $decisions;
    }

    /**
     * @return Decision[]
     */
    public function getDecisions(): array
    {
        return $this->decisions;
    }
}