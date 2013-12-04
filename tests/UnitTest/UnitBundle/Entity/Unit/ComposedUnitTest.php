<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;

class ComposedUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testStringRepresentation(array $components, $expectedId, $expectedSymbol)
    {
        $unit = new ComposedUnit($components);

        $this->assertEquals($expectedId, $unit->getId());
        $this->assertEquals($expectedSymbol, $unit->getSymbol());
        $this->assertEquals($expectedSymbol, $unit->getLabel());
    }

    public function provider()
    {
        $m = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);

        return [
            'm' => [
                [ new UnitComponent($m, 1) ],
                'm',
                'm',
            ],
            'm^2' => [
                [ new UnitComponent($m, 2) ],
                'm^2',
                'm2',
            ],
            'm^-2' => [
                [ new UnitComponent($m, -2) ],
                'm^-2',
                '1/m2',
            ],
            'm.m' => [
                [ new UnitComponent($m, 1), new UnitComponent($m, 1) ],
                'm.m',
                'm.m',
            ],
            'm/m' => [
                [ new UnitComponent($m, -1), new UnitComponent($m, 1) ],
                'm^-1.m',
                'm/m',
            ],
            'm^3.m^-2' => [
                [ new UnitComponent($m, 3), new UnitComponent($m, -2) ],
                'm^3.m^-2',
                'm3/m2',
            ],
            'm^3.m^-2.m^-2' => [
                [ new UnitComponent($m, 3), new UnitComponent($m, -2), new UnitComponent($m, -2) ],
                'm^3.m^-2.m^-2',
                'm3/m2.m2',
            ],
        ];
    }
}
