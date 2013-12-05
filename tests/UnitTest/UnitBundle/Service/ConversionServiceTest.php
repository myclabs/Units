<?php

namespace UnitTest\UnitBundle\Service;

use MyCLabs\UnitAPI\Value;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Service\ConversionService;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

class ConversionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSameUnit()
    {
        $unitExpressionParser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $service = new ConversionService($unitExpressionParser);

        $value = new Value(10, 'm', 5);
        $newValue = $service->convert($value, 'm');

        $this->assertNotSame($value, $newValue, "VO should always be cloned");
        $this->assertEquals($value, $newValue);
    }

    /**
     * @dataProvider provider
     */
    public function testConversion(Value $value, $targetUnit, Value $expectedResult)
    {
        // Mock "m" unit
        $mUnit = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);

        // Mock "km" unit
        $kmUnit = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $kmUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with($mUnit)
            ->will($this->returnValue(1000));

        $mUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with($kmUnit)
            ->will($this->returnValue(1/1000));

        // Mock parser
        $unitExpressionParser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $unitExpressionParser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit) {
                switch ($id) {
                    case 'm':
                        return $mUnit;
                    case 'km':
                        return $kmUnit;
                }
                throw new \Exception("Invalid case");
            }));

        $service = new ConversionService($unitExpressionParser);

        $newValue = $service->convert($value, $targetUnit);

        $this->assertNotSame($value, $newValue, "VO should always be cloned");

        $this->assertEquals($expectedResult->getNumericValue(), $newValue->getNumericValue());
        $this->assertEquals($expectedResult->getUnit(), $newValue->getUnit());
        $this->assertEquals($expectedResult->getUncertainty(), $newValue->getUncertainty());
    }

    public function provider()
    {
        return [
            [ new Value(10, 'm', 5), 'm', new Value(10, 'm', 5) ],
            [ new Value(10, 'km', 5), 'm', new Value(10000, 'm', 5) ],
            [ new Value(10, 'm', 5), 'km', new Value(0.01, 'km', 5) ],
        ];
    }
}
