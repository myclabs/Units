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
     * @dataProvider scenarioProvider
     */
    public function testGetUnit($unitExpression, $expectedSymbol)
    {
        $client = static::createClient();

        $client->request('GET', '/api/en/unit/' . urlencode($unitExpression));

        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        /** @var UnitDTO $unit */
        $unit = json_decode($response->getContent());

        $this->assertEquals($unitExpression, $unit->id);
        $this->assertEquals($expectedSymbol, $unit->symbol);
    }

    public function scenarioProvider()
    {
        return [
            'm'                    => ['m', 'm'],
            'm2'                   => ['m2', 'm²'],
            'm^2'                  => ['m^2', 'm²'],
            'm^2.animal^-1.m^-2.g' => ['m^2.animal^-1.m^-2.g', 'm².g/animal.m²'],
            'm/s'                  => ['m/s', 'm/s'],
        ];
    }

    public function testGetUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/unit/aaa');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('UnknownUnitException: Unknown unit aaa', $response->getContent());
    }

    /**
     * @dataProvider compatibleUnitsProvider
     */
    public function testGetCompatibleUnits($unit, $expectedUnits)
    {
        $client = static::createClient();

        $client->request('GET', '/api/en/compatible-units/' . urlencode($unit));

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
            'm'      => ['m', ['km', '100km', '1000km', 'mile']],
            'm^2'    => ['m^2', ['km^2', '100km^2', '1000km^2', 'mile^2']],
            'animal' => ['animal', []],
        ];
    }

    public function testGetCompatibleUnitsUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/compatible-units/aaa');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('UnknownUnitException: Unknown unit aaa', $response->getContent());
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
