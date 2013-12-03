<?php

namespace UnitTest\UnitBundle\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use MyCLabs\UnitAPI\Value;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Service\ConversionService;

class ConversionServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSameUnit()
    {
        $unitRepository = $this->getMockForAbstractClass(ObjectRepository::class);
        $service = new ConversionService($unitRepository);

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

        // Mock repository
        $unitRepository = $this->getMockForAbstractClass(ObjectRepository::class);
        $unitRepository->expects($this->any())
            ->method('find')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit) {
                return ($id == 'm') ? $mUnit : $kmUnit;
            }));

        $service = new ConversionService($unitRepository);

        $value = new Value(10, 'km', 5);
        $newValue = $service->convert($value, 'm');

        $this->assertNotSame($value, $newValue, "VO should always be cloned");
        $this->assertEquals(10000, $newValue->getNumericValue());
        $this->assertEquals('m', $newValue->getUnit());
        $this->assertEquals(5, $newValue->getUncertainty());
    }
}
