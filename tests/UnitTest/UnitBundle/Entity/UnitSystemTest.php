<?php

namespace UnitTest\UnitBundle\Entity;

use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * @covers \MyCLabs\UnitBundle\Entity\UnitSystem
 */
class UnitSystemTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $unitSystem = new UnitSystem('foo', 'Foo');

        $this->assertEquals('foo', $unitSystem->getId());
        $this->assertEquals('Foo', $unitSystem->getLabel());
    }
}
