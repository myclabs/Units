<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

class StandardUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that getUnitOfReference returns the unit of reference of the physical quantity
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
}
