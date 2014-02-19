<?php

namespace UnitTest\UnitBundle\Service\DTOFactory;

use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Service\DTOFactory\ExceptionDTOFactory;

/**
 * @covers \MyCLabs\UnitBundle\Service\DTOFactory\ExceptionDTOFactory
 */
class ExceptionDTOFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testUnknownUnitException()
    {
        $dtoFactory = new ExceptionDTOFactory();

        $dto = $dtoFactory->create(new UnknownUnitException('Foo', 'bar'));

        $this->assertEquals('UnknownUnitException', $dto['exception']);
        $this->assertEquals('Foo', $dto['message']);
        $this->assertEquals('bar', $dto['unitId']);
    }

    public function testIncompatibleUnitsException()
    {
        $dtoFactory = new ExceptionDTOFactory();

        $dto = $dtoFactory->create(new IncompatibleUnitsException('Foo'));

        $this->assertEquals('IncompatibleUnitsException', $dto['exception']);
        $this->assertEquals('Foo', $dto['message']);
        $this->assertArrayNotHasKey('unitId', $dto);
    }

    public function testOtherException()
    {
        $dtoFactory = new ExceptionDTOFactory();

        $dto = $dtoFactory->create(new \Exception('Foo'));

        $this->assertArrayNotHasKey('exception', $dto);
        $this->assertEquals('Foo', $dto['message']);
    }
}
