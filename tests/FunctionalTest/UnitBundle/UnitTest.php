<?php

namespace FunctionalTest\UnitBundle;

use MyCLabs\UnitAPI\DTO\UnitDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the unit API.
 *
 * @coversNothing
 */
class UnitTest extends WebTestCase
{
    /**
     * @dataProvider getUnitProvider
     */
    public function testGetUnit($unitExpression, $expectedSymbol)
    {
        $client = static::createClient();

        $client->request('GET', '/api/unit/' . urlencode($unitExpression));

        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        /** @var UnitDTO $unit */
        $unit = json_decode($response->getContent());

        $this->assertEquals($unitExpression, $unit->id);
        $this->assertEquals($expectedSymbol, $unit->symbol->en);
    }

    public function getUnitProvider()
    {
        return [
            'm'                    => ['m', 'm'],
            'm2'                   => ['m2', 'm²'],
            'm^2'                  => ['m^2', 'm²'],
            'm^2.animal^-1.m^-2.g' => ['m^2.animal^-1.m^-2.g', 'm².g/animal.m²'],
            'm/s'                  => ['m/s', 'm/s'],
            'kg_co2e'              => ['kg_co2e', 'kg CO2e'],
        ];
    }

    public function testGetUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/unit/aaa');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('UnknownUnitException', $exception->exception);
        $this->assertEquals('Unknown unit aaa', $exception->message);
        $this->assertEquals('aaa', $exception->unitId);
    }

    public function testGetUnitInvalid()
    {
        $client = static::createClient();
        $client->request('GET', '/api/unit/-');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('Invalid unit expression "-": Expected UNIT_ID, but got "-" of type UNKNOWN at beginning of input.', $exception->message);
        $this->assertEquals('-', $exception->unitId);
    }

    /**
     * @dataProvider compatibleUnitsProvider
     */
    public function testGetCompatibleUnits($unit, $expectedUnits)
    {
        $client = static::createClient();

        $client->request('GET', '/api/compatible-units/' . urlencode($unit));

        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        /** @var UnitDTO[] $compatibleUnits */
        $compatibleUnits = json_decode($response->getContent());

        $compatibleUnitsId = array_map(function ($unitDTO) {
            return $unitDTO->id;
        }, $compatibleUnits);

        $this->assertEquals(sort($expectedUnits), sort($compatibleUnitsId));
    }

    public function compatibleUnitsProvider()
    {
        return [
            'm'       => ['m', ['km', '100km', '1000km', 'mile']],
            'm^2'     => ['m^2', ['km^2', '100km^2', '1000km^2', 'mile^2']],
            'animal'  => ['animal', []],
            'kg_co2e' => ['kg_co2e', ['g_co2e', 't_co2e', 'kg_ce', 'g_ce', 't_ce']],
        ];
    }

    public function testGetCompatibleUnitsUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/compatible-units/aaa');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('UnknownUnitException', $exception->exception);
        $this->assertEquals('Unknown unit aaa', $exception->message);
        $this->assertEquals('aaa', $exception->unitId);
    }
    /**
     * @dataProvider getUnitOfReferenceProvider
     */
    public function testGetUnitOfReference($unit, $unitOfReferenceExpected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/unit-of-reference/' . urlencode($unit));

        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        /** @var UnitDTO $unitOfReference */
        $unitOfReference = json_decode($response->getContent());

        $this->assertEquals($unitOfReferenceExpected, $unitOfReference->id);
    }

    public function getUnitOfReferenceProvider()
    {
        return [
            'm'        => ['m', 'm'],
            'km'       => ['km', 'm'],
            'un'       => ['un', 'un'],
            'pourcent' => ['pourcent', 'un'],
            'km.h^-1'  => ['km.h^-1', 'm.s^-1'],
        ];
    }

    protected static function getPhpUnitXmlDir()
    {
        return parent::getPhpUnitXmlDir() . '/app';
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
    }
}
