<?php

namespace MyCLabs\UnitBundle\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use MyCLabs\UnitAPI\Value;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\UnknownUnitException;

/**
 * Service that converts values from a unit to another.
 */
class ConversionService
{
    /**
     * @var ObjectRepository
     */
    private $unitRepository;

    public function __construct(ObjectRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    /**
     * @param Value  $value        Value we want to convert.
     * @param string $targetUnitId Unit in which we want the new value.
     *
     * @throws UnknownUnitException
     * @return Value New value object containing the new unit and the converted value.
     */
    public function convert(Value $value, $targetUnitId)
    {
        // Quick return if same units (no conversion)
        if ($value->getUnit() == $targetUnitId) {
            return clone $value;
        }

        /** @var Unit $unit */
        $unit = $this->unitRepository->find($value->getUnit());
        if ($unit === null) {
            throw UnknownUnitException::create($value->getUnit());
        }

        /** @var Unit $targetUnit */
        $targetUnit = $this->unitRepository->find($targetUnitId);
        if ($targetUnit === null) {
            throw UnknownUnitException::create($targetUnitId);
        }

        $newNumericValue = $value->getNumericValue() * $unit->getConversionFactor($targetUnit);

        return new Value($newNumericValue, $targetUnitId, $value->getUncertainty());
    }
}
