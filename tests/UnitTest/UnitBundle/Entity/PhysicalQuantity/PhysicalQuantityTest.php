<?php

namespace UnitTest\UnitBundle\Entity;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;

/**
 * @covers \MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity
 */
class PhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        /** @var PhysicalQuantity $quantity */
        $quantity = $this->getMockForAbstractClass(
            PhysicalQuantity::class,
            ['test', new TranslatedString('Test', 'en'), 'T']
        );

        $this->assertEquals('test', $quantity->getId());
        $this->assertEquals(new TranslatedString('Test', 'en'), $quantity->getLabel());
        $this->assertEquals('T', $quantity->getSymbol());
    }

    public function testUnitOfReference()
    {
        /** @var PhysicalQuantity $quantity */
        $quantity = $this->getMockForAbstractClass(
            PhysicalQuantity::class,
            ['test', new TranslatedString('Test', 'en'), 'T']
        );

        /** @var StandardUnit $unit */
        $unit = $this->getMockForAbstractClass(StandardUnit::class, [], '', false);

        $quantity->setUnitOfReference($unit);

        $this->assertSame($unit, $quantity->getUnitOfReference());
    }
}
