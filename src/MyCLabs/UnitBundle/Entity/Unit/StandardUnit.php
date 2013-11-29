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
     * Identifiant de la gandeur physique associée à l'unité standard.
     * @var PhysicalQuantity
     */
    protected $physicalQuantity;

    /**
     * Identifiant du système d'unité associé à l'unité standard.
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
        $this->multiplier = $multiplier;
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
    public function getReferenceUnit()
    {
        return $this->physicalQuantity->getUnitOfReference();
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit)
    {
        if (! $unit instanceof StandardUnit) {
            throw new IncompatibleUnitsException(sprintf(
                'Conversion factor impossible: unit %s is only compatible with standard units, %s given',
                $this->label,
                get_class($unit)
            ));
        }

        if ($this->physicalQuantity !== $unit->physicalQuantity) {
            throw new IncompatibleUnitsException(sprintf(
                'Conversion factor impossible: units %s and %s have different physical quantities',
                $this->label,
                $unit->label
            ));
        }

        return $this->multiplier / $unit->multiplier;
    }

    /**
     * Retourne un tableau contenant la conversion de l'unité standard en unités normalisées
     * @return array De la forme ('unit' => StandardUnit, 'exponent' => int).
     */
    public function getNormalizedUnit()
    {
        $tabResults = array();

        /* @var $physicalQuantityComponent Component */
        foreach ($this->getPhysicalQuantity()->getPhysicalQuantityComponents() as $physicalQuantityComponent) {
            $tabResults[] = array(
                'unit'     => $physicalQuantityComponent->getBaseQuantity()->getUnitOfReference(),
                'exponent' => $physicalQuantityComponent->getExponent()
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
}
