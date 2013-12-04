<?php

namespace UnitTest\UnitBundle\Entity\Unit;

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
}
