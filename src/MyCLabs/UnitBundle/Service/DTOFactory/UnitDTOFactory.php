<?php

namespace MyCLabs\UnitBundle\Service\DTOFactory;

use MyCLabs\UnitAPI\DTO\UnitDTO;
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
