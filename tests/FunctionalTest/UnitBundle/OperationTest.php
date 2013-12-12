<?php

namespace FunctionalTest\UnitBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the operations.
 */
class OperationTest extends WebTestCase
{
    /**
     * @dataProvider conversionFactorProvider
     */
    public function testConversionFactor($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/conversion-factor?unit1=' . urlencode($unit1) . '&unit2=' . urlencode($unit2));
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $this->assertEquals($expected, json_decode($response->getContent()));
    }

    public function conversionFactorProvider()
    {
        return [
            [ 'm', 'm', 1 ],
            [ 'km', 'm', 1000 ],
            [ 'km.h^-1', 'km.h^-1', 1 ],
            [ 'km.h^-1', 'm.s^-1', 0.27777777777778 ],
            [ 'm.s^-1', 'km.h^-1', 3.6 ],
            [ 'm^2.animal^-1.m^-2.g.m^2.j^-5', 'animal^-1.g.m^2.j^-5', 1 ],
            [ 'm.m^-2.m^2', 'm', 1 ],
            [ 'kg^2.g', 'kg^3', 0.001 ],
        ];
    }

    /**
     * @dataProvider areCompatibleProvider
     */
    public function testAreCompatible($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/compatible?unit1=' . urlencode($unit1) . '&unit2=' . urlencode($unit2));
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $this->assertSame($expected, json_decode($response->getContent()));
    }

    public function areCompatibleProvider()
    {
        return [
            [ 'm', 'm', true ],
            [ 'km', 'm', true ],
            [ 'km.h^-1', 'm.s^-1', true ],
            [ 'm.s^-1', 'km.h^-1', true ],
            [ 'm^2.animal^-1.m^-2.g.m^2.j^-5', 'animal^-1.g.m^2.j^-5', true ],
            [ 'm.m^-2.m^2', 'm', true ],
            [ 'kg^2.g', 'kg^3', true ],
            [ 'g', 'm', false ],
            [ 'm.g', 'm.m', false ],
            [ 'm^2', 'm^3', false ],
            [ 'm^2', 'm2', false ],
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