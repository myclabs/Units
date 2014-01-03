<?php

namespace UnitTest\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitAPI\Operation\OperationBuilder;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\Operation\MultiplicationExecutor;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * @covers \MyCLabs\UnitBundle\Service\Operation\MultiplicationExecutor
 */
class MultiplicationExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider operationProvider
     */
    public function testExecute(Operation $operation, $expectedUnit, $expectedFactor)
    {
        $executor = new MultiplicationExecutor($this->createParser());
        $result = $executor->execute($operation);

        $this->assertEquals($expectedUnit, $result->getUnitId());
        $this->assertSame($expectedFactor, $result->getConversionFactor());
    }

    public function operationProvider()
    {
        return [
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->getOperation(),
                'm',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('m', 1)
                    ->getOperation(),
                'm^2',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('km', 1)
                    ->getOperation(),
                'm^2',
                1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('km', 1)
                    ->with('km', 1)
                    ->getOperation(),
                'm^2',
                1000. * 1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 2)
                    ->with('km', 2)
                    ->getOperation(),
                'm^4',
                1000. * 1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('m', 1)
                    ->with('m', 1)
                    ->getOperation(),
                'm^3',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('m', 1)
                    ->with('m', -1)
                    ->getOperation(),
                'm',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('kg', 1)
                    ->getOperation(),
                'kg.m',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', -3)
                    ->with('km', 2)
                    ->with('kg', 1)
                    ->getOperation(),
                'kg.m^-1',
                1000. * 1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('m', -1)
                    ->getOperation(),
                '',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 2)
                    ->with('km', -1)
                    ->getOperation(),
                'm',
                1. / 1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('km^2', -1)
                    ->getOperation(),
                'm^-1',
                1. / (1000. * 1000.)
            ],
        ];
    }

    /**
     * @return UnitExpressionParser
     */
    private function createParser()
    {
        // Mock "m" unit
        $mUnit = $this->getMockForAbstractClass(Unit::class, ['m', 'meter', 'm']);
        $mUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with()
            ->will($this->returnValue(1.));
        $mUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($mUnit));
        $mUnit->expects($this->any())
            ->method('getBaseUnitOfReference')
            ->will($this->returnValue($mUnit));
        $mUnit->expects($this->any())
            ->method('pow')
            ->will($this->returnCallback(function ($exponent) use ($mUnit) {
                return new ComposedUnit([ new UnitComponent($mUnit, $exponent) ]);
            }));

        // Mock "km" unit
        $kmUnit = $this->getMockForAbstractClass(Unit::class, ['km', 'kilometer', 'km']);
        $kmUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with()
            ->will($this->returnValue(1000.));
        $kmUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($mUnit));
        $kmUnit->expects($this->any())
            ->method('getBaseUnitOfReference')
            ->will($this->returnValue($mUnit));
        $kmUnit->expects($this->any())
            ->method('pow')
            ->will($this->returnCallback(function ($exponent) use ($kmUnit) {
                return new ComposedUnit([ new UnitComponent($kmUnit, $exponent) ]);
            }));

        // Mock "kg" unit
        $kgUnit = $this->getMockForAbstractClass(Unit::class, ['kg', 'kilogram', 'kg']);
        $kgUnit->expects($this->any())
            ->method('getConversionFactor')
            ->with()
            ->will($this->returnValue(1.));
        $kgUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($kgUnit));
        $kgUnit->expects($this->any())
            ->method('getBaseUnitOfReference')
            ->will($this->returnValue($kgUnit));

        $mUnit->expects($this->any())
            ->method('isCompatibleWith')
            ->with($mUnit)
            ->will($this->returnValue(true));
        $mUnit->expects($this->any())
            ->method('isCompatibleWith')
            ->with($kmUnit)
            ->will($this->returnValue(true));
        $mUnit->expects($this->any())
            ->method('isCompatibleWith')
            ->with($kgUnit)
            ->will($this->returnValue(false));

        // Mock parser
        $parser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit, $kgUnit) {
                switch ($id) {
                    case 'm':
                        return $mUnit;
                    case 'km':
                        return $kmUnit;
                    case 'kg':
                        return $kgUnit;
                    case 'km^2':
                        return new ComposedUnit([new UnitComponent($kmUnit, 2)]);
                }
                throw new \Exception("Invalid case");
            }));

        return $parser;
    }
}
