<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;

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

    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        /** @var Collection $nonNullComponents */
        $nonNullComponents = $this->components->filter(function (Component $component) {
            return $component->getExponent() != 0;
        });

        $unitComponents = $nonNullComponents->map(function (Component $component) {
            return new UnitComponent(
                $component->getBaseQuantity()->getUnitOfReference(),
                $component->getExponent()
            );
        });

        return new ComposedUnit($unitComponents->toArray());
    }
}
