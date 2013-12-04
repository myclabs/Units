<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;
use MyCLabs\UnitBundle\Service\UnitExpressionParser\UnitExpressionLexer;
use UnitTest\UnitBundle\Fixture\FakeUnitRepository;

class ComposedUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider stringRepresentationTestProvider
     */
    public function testStringRepresentation(array $components, $expectedId, $expectedSymbol)
    {
        $unit = new ComposedUnit($components);

        $this->assertEquals($expectedId, $unit->getId());
        $this->assertEquals($expectedSymbol, $unit->getSymbol());
        $this->assertEquals($expectedSymbol, $unit->getLabel());
    }

    public function stringRepresentationTestProvider()
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
    /**
     * @dataProvider getReferenceUnitTestProvider
     */
    public function testGetReferenceUnit($unitExpression, $expectedExpression)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());

        $unit = $parser->parse($unitExpression);
        $expected = $parser->parse($expectedExpression);

        $referenceUnit = $unit->getReferenceUnit();

        $this->assertEquals($expected, $referenceUnit);
    }

    public function getReferenceUnitTestProvider()
    {
        return [
            'm.s' => [
                'm.s',
                'm.s',
            ],
            'km.s' => [
                'km.s',
                'm.s',
            ],
            'km.h^-1' => [
                'km.h^-1',
                'm.s^-1',
            ],
        ];
    }
}
