<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

class DiscreteUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that getUnitOfReference returns itself
     */
    public function testGetUnitOfReference()
    {
        $unit = new DiscreteUnit('m', 'm');

        $this->assertSame($unit, $unit->getUnitOfReference());
    }

    /**
     * Check that getCompatibleUnits returns an empty array.
     */
    public function testGetCompatibleUnits()
    {
        $unit = new DiscreteUnit('m', 'm');

        $this->assertEmpty($unit->getCompatibleUnits());
    }

    public function testPow()
    {
        $unit = new DiscreteUnit('m', 'm');

        $unit2 = $unit->pow(2);

        $this->assertInstanceOf(ComposedUnit::class, $unit2);
        $this->assertEquals('m^2', $unit2->getId());
    }
}
