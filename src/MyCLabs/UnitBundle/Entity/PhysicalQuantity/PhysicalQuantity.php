<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

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
     * Unité de référence de la grandeur physique.
     * @var StandardUnit
     */
    protected $unitOfReference;

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
     * @return StandardUnit
     */
    public function getUnitOfReference()
    {
        return $this->unitOfReference;
    }
}
