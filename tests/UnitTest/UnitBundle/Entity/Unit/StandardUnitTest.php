<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * @covers \MyCLabs\UnitBundle\Entity\Unit\StandardUnit
 */
class StandardUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that getUnitOfReference returns the unit of reference of the physical quantity.
     */
    public function testGetUnitOfReference()
    {
        $system = $this->getMock(UnitSystem::class, [], [], '', false);

        $physicalQuantity = $this->getMock(PhysicalQuantity::class, [], [], '', false);
        $physicalQuantity->expects($this->once())
            ->method('getUnitOfReference')
            ->will($this->returnValue('foo'));

        $unit = new StandardUnit('m', new TranslatedString(), new TranslatedString(), $physicalQuantity, $system, 1);

        $this->assertEquals('foo', $unit->getUnitOfReference());
    }

    /**
     * Check that getCompatibleUnits returns all the other units of the physical quantity.
     */
    public function testGetCompatibleUnits()
    {
        $system = $this->getMock(UnitSystem::class, [], [], '', false);

        /** @var PhysicalQuantity $quantity */
        $quantity = $this->getMockForAbstractClass(
            PhysicalQuantity::class,
            ['l', new TranslatedString(), new TranslatedString()]
        );

        $unit1 = new StandardUnit('m', new TranslatedString(), new TranslatedString(), $quantity, $system, 1);
        $unit2 = new StandardUnit('km', new TranslatedString(), new TranslatedString(), $quantity, $system, 1000);

        $compatibleUnits = $unit1->getCompatibleUnits();

        $this->assertContains($unit2, $compatibleUnits);
        $this->assertNotContains($unit1, $compatibleUnits);
    }

    public function testPow()
    {
        $unit = $this->generateMock();

        $unit2 = $unit->pow(2);

        $this->assertInstanceOf(ComposedUnit::class, $unit2);
        $this->assertEquals('m^2', $unit2->getId());
    }

    private function generateMock()
    {
        $unitSystem = $this->getMock(UnitSystem::class, [], [], '', false);
        $physicalQuantity = $this->getMock(PhysicalQuantity::class, [], [], '', false);
        return new StandardUnit(
            'm',
            new TranslatedString('m', 'en'),
            new TranslatedString('m', 'en'),
            $physicalQuantity,
            $unitSystem,
            1
        );
    }
}
