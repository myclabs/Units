<?php

namespace MyCLabs\UnitAPI;

use MyCLabs\UnitAPI\DTO\UnitDTO;

/**
 * Service that provides units.
 */
interface UnitService
{
    /**
     * @return UnitDTO
     */
    public function getUnits();
}
