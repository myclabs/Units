<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\TranslatedString;

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
     * @param string           $id    External identifier.
     * @param TranslatedString $label Display name.
     */
    public function __construct($id, TranslatedString $label)
    {
        // The symbol is not useful in discrete units, so we use the name
        parent::__construct($id, $label, clone $label);
    }

    /**
     * The unit of reference is itself.
     *
     * {@inheritdoc}
     *
     * @return DiscreteUnit
     */
    public function getUnitOfReference()
    {
        return $this;
    }

    /**
     * The unit of reference is itself.
     *
     * {@inheritdoc}
     *
     * @return DiscreteUnit
     */
    public function getBaseUnitOfReference()
    {
        return $this;
    }

    /**
     * The conversion factor is always 1, as a discrete unit is only compatible with itself.
     *
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit = null)
    {
        if ($unit !== null && $this !== $unit) {
            throw new IncompatibleUnitsException(sprintf(
                'Units "%s" and "%s" are not compatible',
                $this->id,
                $unit->id
            ));
        }

        return 1;
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

    /**
     * {@inheritdoc}
     */
    public function pow($exponent)
    {
        return new ComposedUnit([ new UnitComponent($this, $exponent) ]);
    }
}
