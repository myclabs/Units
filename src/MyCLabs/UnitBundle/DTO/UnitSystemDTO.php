<?php

namespace MyCLabs\UnitBundle\DTO;

use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * Unit system.
 */
class UnitSystemDTO
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
     * @param UnitSystem $unitSystem
     * @return UnitSystemDTO
     */
    public static function create(UnitSystem $unitSystem)
    {
        $dto = new self();

        $dto->id = $unitSystem->getId();
        $dto->label = $unitSystem->getLabel();

        return $dto;
    }

    /**
     * @param UnitSystem[] $unitSystems
     * @return UnitSystemDTO[]
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
