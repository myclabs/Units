<?php

namespace MyCLabs\UnitBundle\DTO;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;

/**
 * Physical quantity.
 */
class PhysicalQuantityDTO
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
     * ID of the unit of reference of this quantity.
     * @var string
     */
    public $unitOfReference;

    /**
     * @param PhysicalQuantity $physicalQuantity
     * @return PhysicalQuantityDTO
     */
    public static function create(PhysicalQuantity $physicalQuantity)
    {
        $dto = new self();

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
    public static function createMany($physicalQuantities)
    {
        $array = [];

        foreach ($physicalQuantities as $physicalQuantity) {
            $array[] = self::create($physicalQuantity);
        }

        return $array;
    }
}
