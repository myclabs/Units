<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * @covers \MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit
 */
class DiscreteUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that getUnitOfReference returns itself
     */
    public function testGetUnitOfReference()
    {
        $unit = new DiscreteUnit('m', new TranslatedString());

        $this->assertSame($unit, $unit->getUnitOfReference());
    }

    /**
     * Check that getBaseUnitOfReference returns itself
     */
    public function testGetBaseUnitOfReference()
    {
        $unit = new DiscreteUnit('m', new TranslatedString());

        $this->assertSame($unit, $unit->getBaseUnitOfReference());
    }

    /**
     * Check that getCompatibleUnits returns an empty array.
     */
    public function testGetCompatibleUnits()
    {
        $unit = new DiscreteUnit('m', new TranslatedString());

        $this->assertEmpty($unit->getCompatibleUnits());
    }

    public function testPow()
    {
        $unit = new DiscreteUnit('m', new TranslatedString('m', 'en'));

        $unit2 = $unit->pow(2);

        $this->assertInstanceOf(ComposedUnit::class, $unit2);
        $this->assertEquals('m^2', $unit2->getId());
    }
}
