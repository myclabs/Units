<?php

namespace MyCLabs\UnitBundle\Service\DTOFactory;

use MyCLabs\UnitAPI\DTO\UnitSystemDTO;
use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * @author matthieu.napoli
 */
class UnitSystemDTOFactory
{
    /**
     * @param UnitSystem $unitSystem
     * @return UnitSystemDTO
     */
    public function create(UnitSystem $unitSystem)
    {
        $dto = new UnitSystemDTO();

        $dto->id = $unitSystem->getId();
        $dto->label = $unitSystem->getLabel();

        return $dto;
    }

    /**
     * @param UnitSystem[] $unitSystems
     * @return UnitSystemDTO[]
     */
    public function createMany($unitSystems)
    {
        $array = [];

        foreach ($unitSystems as $unitSystem) {
            $array[] = $this->create($unitSystem);
        }

        return $array;
    }
}
