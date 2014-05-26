<?php

namespace UnitTest\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;
use MyCLabs\UnitBundle\Service\UnitExpressionParser\UnitExpressionLexer;
use UnitTest\UnitBundle\Fixture\FakeUnitRepository;

/**
 * @covers \MyCLabs\UnitBundle\Entity\Unit\ComposedUnit
 */
class ComposedUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider stringRepresentationTestProvider
     */
    public function testStringRepresentation(array $components, $expectedId, $expectedSymbol)
    {
        $unit = new ComposedUnit($components);

        $this->assertEquals($expectedId, $unit->getId());
        $this->assertEquals($expectedSymbol, $unit->getSymbol()->en);
        $this->assertEquals($expectedSymbol, $unit->getLabel()->en);
    }

    public function stringRepresentationTestProvider()
    {
        $m = $this->getMockForAbstractClass(
            Unit::class,
            ['m', new TranslatedString('Meter', 'en'), new TranslatedString('m', 'en')]
        );

        return [
            'm' => [
                [ new UnitComponent($m, 1) ],
                'm',
                'm',
            ],
            'm^2' => [
                [ new UnitComponent($m, 2) ],
                'm^2',
                'm²',
            ],
            'm^-2' => [
                [ new UnitComponent($m, -2) ],
                'm^-2',
                '1/m²',
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
                'm³/m²',
            ],
            'm^3.m^-2.m^-2' => [
                [ new UnitComponent($m, 3), new UnitComponent($m, -2), new UnitComponent($m, -2) ],
                'm^3.m^-2.m^-2',
                'm³/m².m²',
            ],
        ];
    }

    /**
     * @dataProvider getUnitOfReferenceTestProvider
     */
    public function testGetUnitOfReference($unitExpression, $expectedId)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());

        $unit = $parser->parse($unitExpression);

        $referenceUnit = $unit->getUnitOfReference();

        $this->assertEquals($expectedId, $referenceUnit->getId());
    }

    public function getUnitOfReferenceTestProvider()
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
            'm^2.m^1' => [
                'm^2.m^1',
                'm^3',
            ],
            'm/s' => [
                'm/s',
                'm/s',
            ],
            'knot' => [
                'knot',
                'm/s',
            ],
            'knot.kg' => [
                'knot.kg',
                'kg.m/s',
            ],
            'knot.h' => [
                'knot.h',
                'm/s.s',
            ],
        ];
    }

    /**
     * @dataProvider getBaseUnitOfReferenceTestProvider
     */
    public function testGetBaseUnitOfReference($unitExpression, $expectedId)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());

        $unit = $parser->parse($unitExpression);

        $referenceUnit = $unit->getBaseUnitOfReference();

        $this->assertEquals($expectedId, $referenceUnit->getId());
    }

    public function getBaseUnitOfReferenceTestProvider()
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
            'm^2.m^1' => [
                'm^2.m^1',
                'm^3',
            ],
            'm/s' => [
                'm/s',
                'm.s^-1',
            ],
            'm/s^2' => [
                'm/s^2',
                'm^2.s^-2',
            ],
            'knot' => [
                'knot',
                'm.s^-1',
            ],
            'knot.kg' => [
                'knot.kg',
                'kg.m.s^-1',
            ],
            'knot.h' => [
                'knot.h',
                'm',
            ],
        ];
    }

    /**
     * @dataProvider simplifyProvider
     */
    public function testSimplify($unitExpression, $expectedId)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());

        /** @var ComposedUnit $unit */
        $unit = $parser->parse($unitExpression);

        $this->assertEquals($expectedId, $unit->simplify()->getId());
    }

    public function simplifyProvider()
    {
        return [
            'm.s' => [
                'm.s',
                'm.s',
            ],
            'km.s' => [
                'km.s',
                'km.s',
            ],
            'm^2.m^1' => [
                'm^2.m^1',
                'm^3',
            ],
            'm^2.m^-1' => [
                'm^2.m^-1',
                'm',
            ],
            'm/s' => [
                'm/s',
                'm/s',
            ],
            'm/s.s' => [
                'm/s.s',
                'm/s.s',
            ],
            'h.h^-1' => [
                'h.h^-1',
                '',
            ],
        ];
    }

    /**
     * @dataProvider getConversionFactorProvider
     */
    public function testGetConversionFactor($unitFrom, $unitTo, $expected)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());

        $unitFrom = $parser->parse($unitFrom);
        $unitTo = $parser->parse($unitTo);

        $this->assertSame($expected, $unitFrom->getConversionFactor($unitTo));
    }

    public function getConversionFactorProvider()
    {
        return [
            [ 'm.s', 'm.s', 1. ],
            [ 'km^2', 'm^2', 1000. * 1000. ],
            [ 'km.s', 'm.s', 1000. ],
            [ 'm.s', 'km.s', 0.001 ],
            [ 'm.s^-1', 'km.h^-1', 3.6 ],
        ];
    }

    /**
     * @dataProvider getCompatibleUnitsProvider
     */
    public function testGetCompatibleUnits($unitId, array $expected)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());
        $unit = $parser->parse($unitId);

        $compatibleUnits = $unit->getCompatibleUnits();

        // Turns to string for easier comparison
        $compatibleUnits = array_map(function (Unit $unit) {
            return $unit->getId();
        }, $compatibleUnits);

        $this->assertEquals($expected, array_values($compatibleUnits));
    }

    public function getCompatibleUnitsProvider()
    {
        return [
            'm' => [
                'm',
                [ 'km' ],
            ],
            'km' => [
                'km',
                [ 'm' ],
            ],
            'km.h^-1' => [
                'km.h^-1',
                [ 'km.s^-1', 'm.h^-1', 'm.s^-1' ],
            ],
        ];
    }

    /**
     * @dataProvider powProvider
     */
    public function testPow($unitId, $exponent, $expected)
    {
        $parser = new UnitExpressionParser(new UnitExpressionLexer(), new FakeUnitRepository());
        $unit = $parser->parse($unitId);

        $unit2 = $unit->pow($exponent);

        $this->assertInstanceOf(ComposedUnit::class, $unit2);
        $this->assertEquals($expected, $unit2->getId());
    }

    public function powProvider()
    {
        return [
            'm^1' => [ 'm^1', -1, 'm^-1' ],
            'm^-1' => [ 'm^-1', -1, 'm' ],
            'm^2' => [ 'm^2', -1, 'm^-2' ],
            'm^3' => [ 'm^3', 2, 'm^6' ],
            'm.s' => [ 'm.s', -1, 'm^-1.s^-1' ],
        ];
    }
}
