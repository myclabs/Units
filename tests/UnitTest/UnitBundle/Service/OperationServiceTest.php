<?php

namespace UnitTest\UnitBundle\Service;

use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Service\OperationService;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

class OperationServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider conversionFactorProvider
     */
    public function testConversionFactor($unit1, $unit2, $expected)
    {
        // Mock "m" unit
        $mUnit = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);
        $mUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with($mUnit)
            ->will($this->returnValue(1));

        // Mock "km" unit
        $kmUnit = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $kmUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with($mUnit)
            ->will($this->returnValue(1000));

        // Mock "100km" unit
        $km100Unit = $this->getMockForAbstractClass(Unit::class, ['100km', '100 KiloMeter', '100km']);
        $km100Unit->expects($this->any())
            ->method('getConversionFactor')
            ->with($kmUnit)
            ->will($this->returnValue(100));

        // Mock parser
        $unitExpressionParser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $unitExpressionParser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit, $km100Unit) {
                switch ($id) {
                    case 'm':
                        return $mUnit;
                    case 'km':
                        return $kmUnit;
                    case '100km':
                        return $km100Unit;
                }
                throw new \Exception("Invalid case");
            }));

        $service = new OperationService($unitExpressionParser);

        $this->assertEquals($expected, $service->getConversionFactor($unit1, $unit2));
    }

    public function conversionFactorProvider()
    {
        return [
            [ 'm', 'm', 1 ],
            [ 'km', 'm', 1000 ],
            [ '100km', 'km', 100 ],
        ];
    }
}
