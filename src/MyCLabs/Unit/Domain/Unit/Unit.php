<?php

namespace Unit\Domain\Unit;

use Unit\Domain\IncompatibleUnitsException;

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
    use Translatable;

    /**
     * @var int
     */
    protected $id;

    /**
     * External identifier.
     * @var string
     */
    protected $ref;

    /**
     * Display name.
     * @var string
     */
    protected $name;

    /**
     * Display symbol.
     * @var string
     */
    protected $symbol;

    /**
     * @param string $ref    External identifier.
     * @param string $name   Display name.
     * @param string $symbol Display symbol.
     */
    public function __construct($ref, $name, $symbol)
    {
        $this->ref = $ref;
        $this->name = $name;
        $this->symbol = $symbol;
    }

    /**
     * Returns the external identifier.
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Returns the display name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
