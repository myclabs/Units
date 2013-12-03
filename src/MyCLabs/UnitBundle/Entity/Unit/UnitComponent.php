<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

/**
 * Component of a composed unit.
 *
 * @author matthieu.napoli
 */
class UnitComponent
{
    /**
     * @var Unit
     */
    private $unit;

    /**
     * @var int
     */
    private $exponent;

    public function __construct(Unit $unit, $exponent)
    {
        $this->unit = $unit;
        $this->exponent = (int) $exponent;
    }

    /**
     * @return Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return int
     */
    public function getExponent()
    {
        return $this->exponent;
    }
}
