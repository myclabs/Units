<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;

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
     * @var string
     */
    protected $label;

    /**
     * Display symbol.
     * @var string
     */
    protected $symbol;

    /**
     * Locale for Translatable extension.
     * @var string
     */
    protected $translatableLocale;

    /**
     * @param string $id     Unique identifier.
     * @param string $label  Label.
     * @param string $symbol Display symbol.
     */
    public function __construct($id, $label, $symbol)
    {
        $this->id = (string) $id;
        $this->label = (string) $label;
        $this->symbol = (string) $symbol;
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
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Returns the display symbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Returns the unit of reference.
     *
     * @return Unit
     */
    abstract public function getReferenceUnit();

    /**
     * Returns the conversion factor between this unit and the given unit.
     *
     * @param Unit $unit Must be compatible with this unit.
     *
     * @throws IncompatibleUnitsException
     * @return float
     */
    abstract public function getConversionFactor(Unit $unit);

    /**
     * Returns the list of compatible units, i.e. of same physical quantity.
     *
     * @return Unit[]
     */
    abstract public function getCompatibleUnits();
}
