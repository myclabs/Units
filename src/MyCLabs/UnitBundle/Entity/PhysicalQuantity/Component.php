<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

/**
 * Physical quantity component.
 *
 * @author valentin.claras
 * @author matthieu.napoli
 */
class Component
{
    /**
     * Grandeur physique possédant la grandeur physique de base.
     * @var PhysicalQuantity
     */
    protected $derivedPhysicalQuantity;

    /**
     * Grandeur physique de base possédée par la grandeur physique dérivée.
     * @var PhysicalQuantity
     */
    protected $basePhysicalQuantity;

    /**
     * Exposant que possède la grandeur physique de base au sein de la grandeur physique dérivée.
     * @var int
     */
    protected $exponent;

    public function __construct(
        PhysicalQuantity $derivedPhysicalQuantity,
        PhysicalQuantity $basePhysicalQuantity,
        $exponent
    ) {
        $this->derivedPhysicalQuantity = $derivedPhysicalQuantity;
        $this->basePhysicalQuantity = $basePhysicalQuantity;
        $this->exponent = $exponent;
    }

    /**
     * Renvoi la grandeur physique dérivée.
     * @return PhysicalQuantity
     */
    public function getDerivedPhysicalQuantity()
    {
        return $this->derivedPhysicalQuantity;
    }

    /**
     * Renvoi la grandeur physique de base.
     * @return PhysicalQuantity
     */
    public function getBasePhysicalQuantity()
    {
        return $this->basePhysicalQuantity;
    }

    /**
     * Renvoi l'exposant auquel est associé la grandeur physique de base dans la grandeur physique dérivée.
     * @return int
     */
    public function getExponent()
    {
        return $this->exponent;
    }
}
