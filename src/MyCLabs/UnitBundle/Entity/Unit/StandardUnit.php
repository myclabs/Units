<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * Standard unit.
 *
 * Example: meter, gram, hour...
 *
 * @author valentin.claras
 * @author hugo.charbonnier
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class StandardUnit extends Unit
{
    /**
     * Multiplier from this unit to the standard unit (of the same physical quantity)
     * @var float
     */
    protected $multiplier;

    /**
     * @var PhysicalQuantity
     */
    protected $physicalQuantity;

    /**
     * @var UnitSystem
     */
    protected $unitSystem;

    /**
     * {@inheritdoc}
     * @param PhysicalQuantity $physicalQuantity
     * @param UnitSystem       $unitSystem
     * @param float            $multiplier       Multiplier from this unit to the standard unitk
     */
    public function __construct(
        $id,
        TranslatedString $label,
        TranslatedString $symbol,
        PhysicalQuantity $physicalQuantity,
        UnitSystem $unitSystem,
        $multiplier
    ) {
        parent::__construct($id, $label, $symbol);

        $this->physicalQuantity = $physicalQuantity;
        $this->unitSystem = $unitSystem;
        $this->multiplier = (float) $multiplier;

        $physicalQuantity->addUnit($this);
    }

    /**
     * Returns the multiplier from this unit to the standard unit (of the same physical quantity)
     *
     * @return float
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @return PhysicalQuantity
     */
    public function getPhysicalQuantity()
    {
        return $this->physicalQuantity;
    }

    /**
     * @return UnitSystem
     */
    public function getUnitSystem()
    {
        return $this->unitSystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfReference()
    {
        return $this->physicalQuantity->getUnitOfReference();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        return $this->physicalQuantity->getBaseUnitOfReference();
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit = null)
    {
        if ($unit === null) {
            return $this->getMultiplier();
        }

        if (! $this->isCompatibleWith($unit)) {
            throw new IncompatibleUnitsException(sprintf(
                'Units "%s" and "%s" are not compatible',
                $this->getId(),
                $unit->getId()
            ));
        }

        return $this->getMultiplier() / $unit->getConversionFactor();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleUnits()
    {
        $units = $this->getPhysicalQuantity()->getUnits();

        // Remove this unit from the list
        return array_filter(
            $units,
            function (Unit $unit) {
                return $unit !== $this;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pow($exponent)
    {
        return new ComposedUnit([ new UnitComponent($this, $exponent) ]);
    }
}
