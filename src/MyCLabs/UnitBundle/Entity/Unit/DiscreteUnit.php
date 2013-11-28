<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;

/**
 * Discrete unit.
 *
 * Examples: person, passenger, vehicle, visitor, animal...
 *
 * @author valentin.claras
 * @author hugo.charbonnier
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class DiscreteUnit extends Unit
{
    /**
     * @param string $id  External identifier.
     * @param string $label Display name.
     */
    public function __construct($id, $label)
    {
        // The symbol is not useful in discrete units, so we use the name
        parent::__construct($id, $label, $label);
    }

    /**
     * The unit of reference is itself.
     *
     * {@inheritdoc}
     *
     * @return DiscreteUnit
     */
    public function getReferenceUnit()
    {
        return $this;
    }

    /**
     * The conversion factor is always 1, as a discrete unit is only compatible with itself.
     *
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit)
    {
        if ($this !== $unit) {
            throw new IncompatibleUnitsException('Units need to be the same');
        }

        return 1.;
    }

    /**
     * A discrete unit has no compatible unit.
     *
     * {@inheritdoc}
     */
    public function getCompatibleUnits()
    {
        return [];
    }
}
