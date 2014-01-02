<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
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
        $unitSystem = $this->getMock(UnitSystem::class, [], [], '', false);

        $physicalQuantity = $this->getMock(PhysicalQuantity::class, [], [], '', false);
        $physicalQuantity->expects($this->once())
            ->method('getUnitOfReference')
            ->will($this->returnValue('foo'));

        $unit = new StandardUnit('m', 'm', 'm', $physicalQuantity, $unitSystem, 1);

        $this->assertEquals('foo', $unit->getUnitOfReference());
    }

    /**
     * Check that getCompatibleUnits returns all the other units of the physical quantity.
     */
    public function testGetCompatibleUnits()
    {
        $unitSystem = $this->getMock(UnitSystem::class, [], [], '', false);

        /** @var PhysicalQuantity $physicalQuantity */
        $physicalQuantity = $this->getMockForAbstractClass(PhysicalQuantity::class, ['l', 'Length', 'L']);

        $unit1 = new StandardUnit('m', 'm', 'm', $physicalQuantity, $unitSystem, 1);
        $unit2 = new StandardUnit('km', 'km', 'km', $physicalQuantity, $unitSystem, 1000);

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
        return new StandardUnit('m', 'm', 'm', $physicalQuantity, $unitSystem, 1);
    }
}
