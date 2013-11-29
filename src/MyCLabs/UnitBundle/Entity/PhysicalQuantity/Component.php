<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

/**
 * A derived quantity is composed of base quantities. This class represents each component.
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
    protected $derivedQuantity;

    /**
     * Grandeur physique de base possédée par la grandeur physique dérivée.
     * @var PhysicalQuantity
     */
    protected $baseQuantity;

    /**
     * Exposant que possède la grandeur physique de base au sein de la grandeur physique dérivée.
     * @var int
     */
    protected $exponent;

    public function __construct(PhysicalQuantity $derivedQuantity, PhysicalQuantity $baseQuantity, $exponent)
    {
        $this->derivedQuantity = $derivedQuantity;
        $this->baseQuantity = $baseQuantity;
        $this->exponent = $exponent;
    }

    /**
     * Renvoi la grandeur physique dérivée.
     * @return PhysicalQuantity
     */
    public function getDerivedQuantity()
    {
        return $this->derivedQuantity;
    }

    /**
     * Renvoi la grandeur physique de base.
     * @return PhysicalQuantity
     */
    public function getBaseQuantity()
    {
        return $this->baseQuantity;
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
