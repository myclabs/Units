<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\TranslatedString;

/**
 * Unit.
 *
 * @author valentin.claras
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
abstract class Unit
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Label.
     * @var TranslatedString
     */
    protected $label;

    /**
     * Display symbol.
     * @var TranslatedString
     */
    protected $symbol;

    /**
     * @param string           $id     Unique identifier.
     * @param TranslatedString $label  Label.
     * @param TranslatedString $symbol Display symbol.
     */
    public function __construct($id, TranslatedString $label, TranslatedString $symbol)
    {
        $this->id = (string) $id;
        $this->label = $label;
        $this->symbol = $symbol;
    }

    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the label.
     *
     * @return TranslatedString
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the display symbol.
     *
     * @return TranslatedString
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Returns the unit of reference in the same physical quantity.
     *
     * @return Unit
     */
    abstract public function getUnitOfReference();

    /**
     * Returns the unit of reference in the base physical quantity.
     *
     * @return Unit
     */
    abstract public function getBaseUnitOfReference();

    /**
     * Returns the conversion factor between this unit and the given unit.
     * If no unit is given, then the conversion factor is between this unit and the unit of reference.
     *
     * @param Unit|null $unit Must be compatible with this unit.
     *
     * @throws IncompatibleUnitsException
     * @return float
     */
    abstract public function getConversionFactor(Unit $unit = null);

    /**
     * Returns the list of compatible units, i.e. of same physical quantity.
     *
     * @return Unit[]
     */
    abstract public function getCompatibleUnits();

    /**
     * Returns true if the unit is compatible (convertible to) with the given unit.
     *
     * @param Unit $unit
     *
     * @return boolean
     */
    public function isCompatibleWith(Unit $unit)
    {
        return ($this->getBaseUnitOfReference()->getId() === $unit->getBaseUnitOfReference()->getId());
    }

    /**
     * Inverse the unit (inverse exponents).
     *
     * @return Unit
     */
    public function inverse()
    {
        return $this->pow(-1);
    }

    /**
     * Apply the exponent to the unit.
     *
     * Examples:
     * - pow(m, 2)   -> m^2
     * - pow(m^2, 2) -> m^4
     * - pow(m2, 2)  -> m2^2
     *
     * @param int $exponent
     *
     * @return Unit
     */
    abstract public function pow($exponent);

    /**
     * Simplify the unit expression by merging components in the same unit.
     *
     * For example, the composed unit "m^2.m^-1" will return the standard unit "m".
     *
     * @return Unit
     */
    public function simplify()
    {
        return $this;
    }
}
