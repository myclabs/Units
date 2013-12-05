<?php

namespace FunctionalTest\UnitBundle;

use MyCLabs\UnitAPI\DTO\UnitDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the unit API.
 */
class UnitTest extends WebTestCase
{
    /**
     * @dataProvider scenarioProvider
     */
    public function testGetUnit($unitExpression, $expectedSymbol)
    {
        $client = static::createClient();

        $client->request('GET', '/api/unit/' . urlencode($unitExpression) . '/');

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
        ];
    }

    protected static function getPhpUnitXmlDir()
    {
        return parent::getPhpUnitXmlDir() . '/app';
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }
}
