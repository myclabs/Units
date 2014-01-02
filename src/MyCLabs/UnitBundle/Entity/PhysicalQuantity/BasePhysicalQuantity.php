<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

/**
 * Base physical quantity (M, L, T).
 *
 * @author matthieu.napoli
 */
class BasePhysicalQuantity extends PhysicalQuantity
{
    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        return $this->getUnitOfReference();
    }
}
