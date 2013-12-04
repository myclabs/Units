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

    public function testConversion()
    {
        // Mock "m" unit
        $mUnit = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);

        // Mock "km" unit
        $kmUnit = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $kmUnit->expects($this->once())
            ->method('getConversionFactor')
            ->with($mUnit)
            ->will($this->returnValue(1000));

        // Mock parser
        $unitExpressionParser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $unitExpressionParser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit) {
                return ($id == 'm') ? $mUnit : $kmUnit;
            }));

        $service = new ConversionService($unitExpressionParser);

        $value = new Value(10, 'km', 5);
        $newValue = $service->convert($value, 'm');

        $this->assertNotSame($value, $newValue, "VO should always be cloned");
        $this->assertEquals(10000, $newValue->getNumericValue());
        $this->assertEquals('m', $newValue->getUnit());
        $this->assertEquals(5, $newValue->getUncertainty());
    }
}
