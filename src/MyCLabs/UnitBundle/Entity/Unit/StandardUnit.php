<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\Component;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
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
        $label,
        $symbol,
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
    public function getConversionFactor(Unit $unit = null)
    {
        if ($unit === null) {
            return $this->getMultiplier();
        }

        if (! $unit instanceof StandardUnit) {
            throw new IncompatibleUnitsException(sprintf(
                'Conversion factor impossible: unit "%s" is only compatible with standard units, %s given',
                $this->id,
                get_class($unit)
            ));
        }

        if ($this->getPhysicalQuantity() !== $unit->getPhysicalQuantity()) {
            throw new IncompatibleUnitsException(sprintf(
                'Conversion factor impossible: units "%s" and "%s" have different physical quantities: '
                . '"%s" and "%s"',
                $this->id,
                $unit->id,
                $this->getPhysicalQuantity()->getId(),
                $unit->getPhysicalQuantity()->getId()
            ));
        }

        return $this->getMultiplier() / $unit->getMultiplier();
    }

    /**
     * Retourne un tableau contenant la conversion de l'unité standard en unités normalisées
     * @return array De la forme ('unit' => StandardUnit, 'exponent' => int).
     */
    public function getNormalizedUnit()
    {
        // TODO remove?
        $tabResults = array();

        /* @var $component Component */
        foreach ($this->getPhysicalQuantity()->getComponents() as $component) {
            $tabResults[] = array(
                'unit'     => $component->getBaseQuantity()->getUnitOfReference(),
                'exponent' => $component->getExponent()
            );
        }

        return $tabResults;
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
