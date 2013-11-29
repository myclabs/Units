<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Physical quantity derived from the base physical quantities.
 *
 * @author matthieu.napoli
 */
class DerivedPhysicalQuantity extends PhysicalQuantity
{
    /**
     * @var Collection|Component[]
     */
    protected $components;

    public function __construct($id, $symbol, $label)
    {
        parent::__construct($id, $symbol, $label);

        $this->components = new ArrayCollection();
    }

    /**
     * @param BasePhysicalQuantity $basePhysicalQuantity
     * @param int                  $exponent
     */
    public function addComponent(BasePhysicalQuantity $basePhysicalQuantity, $exponent)
    {
        $physicalQuantityComponent = new Component($this, $basePhysicalQuantity, $exponent);
        $this->components->add($physicalQuantityComponent);
    }

    /**
     * @return PhysicalQuantity[]
     */
    public function getComponents()
    {
        return $this->components->toArray();
    }
}
