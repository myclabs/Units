<?php

namespace MyCLabs\UnitBundle\DTO;

use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;

/**
 * Unit.
 */
class UnitDTO
{
    /**
     * Identifier.
     * @var string
     */
    public $id;

    /**
     * Label.
     * @var string
     */
    public $label;

    /**
     * Symbol.
     * @var string
     */
    public $symbol;

    /**
     * ID of the unit system.
     * @var string
     */
    public $unitSystem;

    /**
     * @param Unit $unit
     * @return UnitDTO
     */
    public static function create(Unit $unit)
    {
        $dto = new self();

        $dto->id = $unit->getId();
        $dto->label = $unit->getLabel();
        $dto->symbol = $unit->getSymbol();

        if ($unit instanceof StandardUnit) {
            $dto->unitSystem = $unit->getUnitSystem()->getId();
        }

        return $dto;
    }

    /**
     * @param Unit[] $unitSystems
     * @return UnitDTO[]
     */
    public static function createMany($unitSystems)
    {
        $array = [];

        foreach ($unitSystems as $unitSystem) {
            $array[] = self::create($unitSystem);
        }

        return $array;
    }
}
