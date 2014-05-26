<?php

namespace UnitTest\UnitBundle\Service;

use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;
use MyCLabs\UnitBundle\Service\UnitExpressionParser\UnitExpressionLexer;

/**
 * @covers \MyCLabs\UnitBundle\Service\UnitExpressionParser
 */
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
    private $m2;
    private $ms;
    private $kgco2e;

    public function setUp()
    {
        // Mock units
        $this->m = $this->getMockForAbstractClass(Unit::class, ['m', new TranslatedString(), new TranslatedString()]);
        $this->km = $this->getMockForAbstractClass(Unit::class, ['km', new TranslatedString(), new TranslatedString()]);
        $this->s = $this->getMockForAbstractClass(Unit::class, ['s', new TranslatedString(), new TranslatedString()]);
        $this->m2 = $this->getMockForAbstractClass(Unit::class, ['m2', new TranslatedString(), new TranslatedString()]);
        $this->ms = $this->getMockForAbstractClass(Unit::class, ['m/s', new TranslatedString(), new TranslatedString()]);
        $this->kgco2e = $this->getMockForAbstractClass(Unit::class, ['kg_co2e', new TranslatedString(), new TranslatedString()]);

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
                    case 'm2':
                        return $this->m2;
                    case 'm/s':
                        return $this->ms;
                    case 'kg_co2e':
                        return $this->kgco2e;
                }
                throw UnknownUnitException::create($id);
            }));

        $this->service = new UnitExpressionParser(new UnitExpressionLexer(), $this->unitRepository);
    }

    /**
     * @dataProvider standardUnitProvider
     */
    public function testParseStandardUnit($expression)
    {
        /** @var StandardUnit $unit */
        $unit = $this->service->parse($expression);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals($expression, $unit->getId());
    }

    public function standardUnitProvider()
    {
        return [
            ['m'],
            ['km'],
            ['s'],
            ['m2'],
            ['m/s'],
            ['kg_co2e'],
        ];
    }

    /**
     * @dataProvider composedUnitProvider
     */
    public function testParseComposedUnit($expression, $expectedComponents)
    {
        /** @var ComposedUnit $unit */
        $unit = $this->service->parse($expression);

        $this->assertInstanceOf(ComposedUnit::class, $unit);
        $this->assertEquals($expectedComponents, $this->readAttribute($unit, 'components'));
    }

    public function composedUnitProvider()
    {
        $m = $this->getMockForAbstractClass(Unit::class, ['m', new TranslatedString(), new TranslatedString()]);
        $km = $this->getMockForAbstractClass(Unit::class, ['km', new TranslatedString(), new TranslatedString()]);
        $s = $this->getMockForAbstractClass(Unit::class, ['s', new TranslatedString(), new TranslatedString()]);
        $ms = $this->getMockForAbstractClass(Unit::class, ['m/s', new TranslatedString(), new TranslatedString()]);
        $kgco2e = $this->getMockForAbstractClass(Unit::class, ['kg_co2e', new TranslatedString(), new TranslatedString()]);

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
            // Tolerant to "/"
            'm/s . s' => [
                'm/s . s',
                [
                    new UnitComponent($ms, 1),
                    new UnitComponent($s, 1),
                ]
            ],
            // Tolerant to "_"
            'm . kg_co2e' => [
                'm . kg_co2e',
                [
                    new UnitComponent($m, 1),
                    new UnitComponent($kgco2e, 1),
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidSyntaxProvider
     * @expectedException \MyCLabs\UnitAPI\Exception\UnknownUnitException
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
