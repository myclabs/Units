<?php

namespace UnitTest\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitAPI\Operation\OperationBuilder;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Service\Operation\AdditionExecutor;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

class AdditionExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider operationProvider
     */
    public function testExecute(Operation $operation, $expected)
    {
        $executor = new AdditionExecutor($this->createParser());
        $result = $executor->execute($operation);

        $this->assertEquals($expected, $result);
    }

    public function operationProvider()
    {
        return [
            [
                OperationBuilder::addition()
                    ->with('m', 1)
                    ->with('m', 1)
                    ->getOperation(),
                'm'
            ],
            [
                OperationBuilder::addition()
                    ->with('m', 1)
                    ->with('km', 1)
                    ->getOperation(),
                'm'
            ],
            [
                OperationBuilder::addition()
                    ->with('km', 1)
                    ->with('km', 1)
                    ->getOperation(),
                'm'
            ],
            [
                OperationBuilder::addition()
                    ->with('m', 2)
                    ->with('km', 2)
                    ->getOperation(),
                'm^2'
            ],
        ];
    }
    /**
     * @dataProvider failingOperationProvider
     * @expectedException \MyCLabs\UnitAPI\Exception\IncompatibleUnitsException
     * @expectedExceptionMessage In an addition, components must have compatible units.
     */
    public function testExecuteException(Operation $operation)
    {
        $executor = new AdditionExecutor($this->createParser());
        $executor->execute($operation);
    }

    public function failingOperationProvider()
    {
        return [
            [
                OperationBuilder::addition()
                    ->with('m', 1)
                    ->with('g', 1)
                    ->getOperation()
            ],
            [
                OperationBuilder::addition()
                    ->with('m', 1)
                    ->with('m', 2)
                    ->getOperation()
            ],
        ];
    }

    /**
     * @return UnitExpressionParser
     */
    private function createParser()
    {
        // Mock "m" unit
        $mUnit = $this->getMockForAbstractClass(Unit::class, ['m', 'Meter', 'm']);
        $mUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($mUnit));
        $mUnit->expects($this->any())
            ->method('pow')
            ->with(2)
            ->will($this->returnValue(new ComposedUnit([ new UnitComponent($mUnit, 2) ])));

        // Mock "km" unit
        $kmUnit = $this->getMockForAbstractClass(Unit::class, ['km', 'KiloMeter', 'km']);
        $kmUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($mUnit));
        $kmUnit->expects($this->any())
            ->method('pow')
            ->with(2)
            ->will($this->returnValue(new ComposedUnit([ new UnitComponent($kmUnit, 2) ])));

        // Mock "g" unit
        $gUnit = $this->getMockForAbstractClass(Unit::class, ['g', 'Gram', 'g']);
        $gUnit->expects($this->any())
            ->method('getUnitOfReference')
            ->will($this->returnValue($gUnit));

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
            ->with($gUnit)
            ->will($this->returnValue(false));

        // Mock parser
        $parser = $this->getMock(UnitExpressionParser::class, [], [], '', false);
        $parser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($id) use ($mUnit, $kmUnit, $gUnit) {
                switch ($id) {
                    case 'm':
                        return $mUnit;
                    case 'km':
                        return $kmUnit;
                    case 'g':
                        return $gUnit;
                }
                throw new \Exception("Invalid case");
            }));

        return $parser;
    }
}
