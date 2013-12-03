<?php

namespace MyCLabs\UnitAPI;

use MyCLabs\UnitBundle\Entity\UnknownUnitException;

/**
 * Service that converts values from a unit to another.
 */
interface ConversionService
{
    /**
     * @param Value  $value        Value we want to convert.
     * @param string $targetUnitId Unit in which we want the new value.
     *
     * @throws UnknownUnitException
     * @return Value New value object containing the new unit and the converted value.
     */
    public function convert(Value $value, $targetUnitId);
}
