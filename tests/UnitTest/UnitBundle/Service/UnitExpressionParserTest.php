<?php

namespace UnitTest\UnitBundle\Service;

use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;
use MyCLabs\UnitBundle\Service\UnitExpressionParser\UnitExpressionLexer;

class UnitExpressionParserTest extends \PHPUnit_Framework_TestCase
{
    private $unitRepository;
    /**
     * @var UnitExpressionParser
     */
    private $service;

    private $m;
    private $km;
    private $s;

    public function setUp()
    {
        // Mock units
        $this->m = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);
        $this->km = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $this->s = $this->getMockForAbstractClass(Unit::class, ['s', 'Second', 's']);

        // Mock unit repository
        $this->unitRepository = $this->getMockForAbstractClass(UnitRepository::class);
        $this->unitRepository->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function ($id) {
                switch ($id) {
                    case 'm':
                        return $this->m;
                    case 'km':
                        return $this->km;
                    case 's':
                        return $this->s;
                }
                return null;
            }));

        $this->service = new UnitExpressionParser(new UnitExpressionLexer(), $this->unitRepository);
    }

    public function testParseSimpleUnit()
    {
        $this->assertSame($this->m, $this->service->parse('m'));
    }

    /**
     * @dataProvider provider
     */
    public function testParseComposedUnit($expression, $expectedComponents)
    {
        /** @var ComposedUnit $unit */
        $unit = $this->service->parse($expression);

        $this->assertInstanceOf(ComposedUnit::class, $unit);
        $this->assertEquals($expectedComponents, $this->readAttribute($unit, 'components'));
    }

    public function provider()
    {
        $m = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);
        $km = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $s = $this->getMockForAbstractClass(Unit::class, ['s', 'Second', 's']);

        return [
            'm^2' => [
                'm^2',
                [
                    new UnitComponent($m, 2),
                ]
            ],
            'm.m' => [
                'm.m',
                [
                    new UnitComponent($m, 1),
                    new UnitComponent($m, 1),
                ]
            ],
            'm.km' => [
                'm.km',
                [
                    new UnitComponent($m, 1),
                    new UnitComponent($km, 1),
                ]
            ],
            'm^2.s^-1' => [
                'm^2.s^-1',
                [
                    new UnitComponent($m, 2),
                    new UnitComponent($s, -1),
                ]
            ],
            // Tolerant to spaces
            ' m^1' => [
                ' m^1',
                [
                    new UnitComponent($m, 1),
                ]
            ],
            // Tolerant to spaces
            'm . s' => [
                'm . s',
                [
                    new UnitComponent($m, 1),
                    new UnitComponent($s, 1),
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidSyntaxProvider
     * @expectedException \MyCLabs\UnitBundle\Service\UnitExpressionParser\InvalidUnitSyntaxException
     */
    public function testInvalidSyntax1($str)
    {
        $this->service->parse($str);
    }

    public function invalidSyntaxProvider()
    {
        return [
            [ '' ],
            [ '^2' ],
            [ '.' ],
            [ 'a b' ],
        ];
    }
}
