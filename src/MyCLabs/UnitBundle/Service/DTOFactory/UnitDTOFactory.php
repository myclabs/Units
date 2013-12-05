<?php

namespace MyCLabs\UnitBundle\Service\DTOFactory;

use MyCLabs\UnitAPI\DTO\UnitDTO;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;

/**
 * @author matthieu.napoli
 */
class UnitDTOFactory
{
    /**
     * @param Unit $unit
     * @return UnitDTO
     */
    public function create(Unit $unit)
    {
        $dto = new UnitDTO();

        $dto->id = $unit->getId();
        $dto->label = $unit->getLabel();
        $dto->symbol = $unit->getSymbol();

        if ($unit instanceof StandardUnit) {
            $dto->unitSystem = $unit->getUnitSystem()->getId();
        }

        switch (true) {
            case $unit instanceof StandardUnit:
                $dto->type = UnitDTO::TYPE_STANDARD;
                break;
            case $unit instanceof DiscreteUnit:
                $dto->type = UnitDTO::TYPE_DISCRETE;
                break;
            case $unit instanceof ComposedUnit:
                $dto->type = UnitDTO::TYPE_COMPOSED;
                break;
        }

        return $dto;
    }

    /**
     * @param Unit[] $units
     * @return UnitDTO[]
     */
    public function createMany($units)
    {
        $array = [];

        foreach ($units as $unitSystem) {
            $array[] = $this->create($unitSystem);
        }

        return $array;
    }
}
