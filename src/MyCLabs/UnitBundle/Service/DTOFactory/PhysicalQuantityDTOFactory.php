<?php

namespace MyCLabs\UnitBundle\Service\DTOFactory;

use MyCLabs\UnitAPI\DTO\PhysicalQuantityDTO;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;

/**
 * @author matthieu.napoli
 */
class PhysicalQuantityDTOFactory
{
    /**
     * @param PhysicalQuantity $physicalQuantity
     * @return PhysicalQuantityDTO
     */
    public function create(PhysicalQuantity $physicalQuantity)
    {
        $dto = new PhysicalQuantityDTO();

        $dto->id = $physicalQuantity->getId();
        $dto->label = $physicalQuantity->getLabel();
        $dto->symbol = $physicalQuantity->getSymbol();
        $dto->unitOfReference = $physicalQuantity->getUnitOfReference()->getId();

        return $dto;
    }

    /**
     * @param PhysicalQuantity[] $physicalQuantities
     * @return PhysicalQuantityDTO[]
     */
    public function createMany($physicalQuantities)
    {
        $array = [];

        foreach ($physicalQuantities as $physicalQuantity) {
            $array[] = $this->create($physicalQuantity);
        }

        return $array;
    }
}
