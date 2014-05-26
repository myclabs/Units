<?php

namespace UnitTest\UnitBundle\Entity;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\DerivedPhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;

/**
 * @covers \MyCLabs\UnitBundle\Entity\PhysicalQuantity\DerivedPhysicalQuantity
 */
class DerivedPhysicalQuantityTest extends \PHPUnit_Framework_TestCase
{
    public function testBaseUnitOfReference()
    {
        /** @var StandardUnit $m */
        $m = $this->getMockForAbstractClass(StandardUnit::class, [], '', false);
        /** @var StandardUnit $s */
        $s = $this->getMockForAbstractClass(StandardUnit::class, [], '', false);
        /** @var StandardUnit $kg */
        $kg = $this->getMockForAbstractClass(StandardUnit::class, [], '', false);

        $length = new BasePhysicalQuantity('l', new TranslatedString(), 'L');
        $length->setUnitOfReference($m);
        $time = new BasePhysicalQuantity('t', new TranslatedString(), 'T');
        $time->setUnitOfReference($s);
        $mass = new BasePhysicalQuantity('m', new TranslatedString(), 'M');
        $mass->setUnitOfReference($kg);

        $speed = new DerivedPhysicalQuantity('s', new TranslatedString(), 'S');
        $speed->addComponent($length, 1);
        $speed->addComponent($time, -1);
        $speed->addComponent($mass, 0);

        /** @var ComposedUnit $unit */
        $unit = $speed->getBaseUnitOfReference();

        $this->assertInstanceOf(ComposedUnit::class, $unit);
        $expectedComponents = [
            new UnitComponent($m, 1),
            new UnitComponent($s, -1),
        ];
        $this->assertAttributeEquals($expectedComponents, 'components', $unit);
    }
}
