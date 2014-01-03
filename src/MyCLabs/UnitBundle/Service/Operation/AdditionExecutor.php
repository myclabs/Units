<?php

namespace MyCLabs\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Operation\Addition;
use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException as APIIncompatibleUnitsException;
use MyCLabs\UnitAPI\Operation\OperationComponent;
use MyCLabs\UnitAPI\Operation\Result\AdditionResult;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * Executes an operation of type Addition.
 *
 * @author matthieu.napoli
 */
class AdditionExecutor implements OperationExecutor
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
        return $operation instanceof Addition;
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

        /** @var Unit $firstUnit */
        $firstUnit = $units[0];

        // For an addition, we check that components are compatible
        array_walk($units, function (Unit $unit, $index) use ($firstUnit) {
            if (! $unit->isCompatibleWith($firstUnit)) {
                throw new APIIncompatibleUnitsException(sprintf(
                    'In an addition, components must have compatible units. '
                    . 'Component %d (unit "%s") is not compatible with the first unit of the operation (unit "%s")',
                    $index,
                    $unit->getId(),
                    $firstUnit->getId()
                ));
            }
        });

        // Since all components are compatibles, we take the first unit (actually, its base unit of reference)
        $unitId = $firstUnit->getBaseUnitOfReference()->getId();

        return new AdditionResult($unitId);
    }
}
