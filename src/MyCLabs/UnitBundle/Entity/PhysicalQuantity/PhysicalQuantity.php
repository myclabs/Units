<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;

/**
 * Physical quantity.
 *
 * @author valentin.claras
 * @author hugo.charbonnier
 * @author yoann.croizer
 * @author matthieu.napoli
 */
abstract class PhysicalQuantity
{
    /**
     * Identifier.
     * @var string
     */
    protected $id;

    /**
     * Label.
     * @var string
     */
    protected $label;

    /**
     * Symbol.
     * @var string
     */
    protected $symbol;

    /**
     * Unit of reference for the physical quantity.
     * @var StandardUnit
     */
    protected $unitOfReference;

    /**
     * Units from this physical quantity.
     * @var StandardUnit[]|Collection
     */
    protected $units;

    /**
     * Locale for Translatable extension.
     * @var string
     */
    protected $translatableLocale;


    public function __construct($id, $label, $symbol)
    {
        $this->id = $id;
        $this->label = $label;
        $this->symbol = $symbol;

        $this->units = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Defines the new unit of reference for this physical quantity.
     *
     * @param StandardUnit $unit
     */
    public function setUnitOfReference(StandardUnit $unit)
    {
        $this->unitOfReference = $unit;
    }

    /**
     * Returns the unit of reference for this physical quantity.
     *
     * @throws \RuntimeException No unit of reference was previously defined.
     * @return StandardUnit
     */
    public function getUnitOfReference()
    {
        if ($this->unitOfReference === null) {
            throw new \RuntimeException("No unit of reference was defined for the quantity $this->id");
        }

        return $this->unitOfReference;
    }

    /**
     * @return StandardUnit[] Units from this physical quantity.
     */
    public function getUnits()
    {
        return $this->units->toArray();
    }

    /**
     * @param StandardUnit $unit
     */
    public function addUnit(StandardUnit $unit)
    {
        $this->units->add($unit);
    }
}
