<?php

namespace MyCLabs\UnitBundle\Service;

use MyCLabs\UnitAPI\Value;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\UnknownUnitException;

/**
 * Service that converts values from a unit to another.
 */
class ConversionService implements \MyCLabs\UnitAPI\ConversionService
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
    public function convert(Value $value, $targetUnit)
    {
        // Quick return if same units (no conversion)
        if ($value->getUnit() == $targetUnit) {
            return clone $value;
        }

        /** @var Unit $unit */
        $unit = $this->unitExpressionParser->parse($value->getUnit());
        if ($unit === null) {
            throw UnknownUnitException::create($value->getUnit());
        }

        /** @var Unit $targetUnit */
        $targetUnit = $this->unitExpressionParser->parse($targetUnit);
        if ($targetUnit === null) {
            throw UnknownUnitException::create($targetUnit);
        }

        $newNumericValue = $value->getNumericValue() * $unit->getConversionFactor($targetUnit);

        return new Value($newNumericValue, $targetUnit->getId(), $value->getUncertainty());
    }
}
