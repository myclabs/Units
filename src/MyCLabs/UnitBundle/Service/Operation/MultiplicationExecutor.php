<?php

namespace MyCLabs\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Operation\Multiplication;
use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitAPI\Operation\OperationComponent;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * Executes an operation of type Multiplication.
 *
 * @author matthieu.napoli
 */
class MultiplicationExecutor implements OperationExecutor
{
    /**
     * @var UnitExpressionParser
     */
    private $unitExpressionParser;

    public function __construct(UnitExpressionParser $unitExpressionParser)
    {
        $this->unitExpressionParser = $unitExpressionParser;
    }

    /**
     * {@inheritdoc}
     */
    public function handles(Operation $operation)
    {
        return $operation instanceof Multiplication;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Operation $operation)
    {
        if (! $this->handles($operation)) {
            throw new \InvalidArgumentException('Unhandled operation of type ' . get_class($operation));
        }

        $components = $operation->getComponents();

        // Apply the exponent of each component
        $units = array_map(function (OperationComponent $component) {
            $unit = $this->unitExpressionParser->parse($component->getUnitId());

            if ($component->getExponent() === 1) {
                return $unit;
            }

            return $unit->pow($component->getExponent());
        }, $components);

        // Turn each unit into its unit of reference
        $units = array_map(function (Unit $unit) {
            return $unit->getBaseUnitOfReference();
        }, $units);

        // Flatten everything to unit components (m.s^-1 * s => m * s^-1 * s)
        $unitComponents = [];
        foreach ($units as $unit) {
            if ($unit instanceof ComposedUnit) {
                foreach ($unit->getComponents() as $unitComponent) {
                    $unitComponents[] = $unitComponent;
                }
            } else {
                $unitComponents[] = new UnitComponent($unit, 1);
            }
        }

        $resultUnit = new ComposedUnit($unitComponents);
        $resultUnit = $resultUnit->simplify();

        return $resultUnit->getId();
    }
}
