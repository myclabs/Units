<?php

namespace FunctionalTest\UnitBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the conversion factor.
 */
class ConversionFactorTest extends WebTestCase
{
    /**
     * @dataProvider scenarioProvider
     */
    public function testConvert($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/conversion-factor/' . urlencode($unit1) . '/' . urlencode($unit2));
        die('/api/conversion-factor/' . urlencode($unit1) . '/' . urlencode($unit2));
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $conversionFactor = json_decode($response->getContent());

        $this->assertEquals($expected, $conversionFactor);
    }

    public function scenarioProvider()
    {
        return [
//            [ 'm', 'm', 1 ],
//            [ 'km', 'm', 1000 ],
            [ 'km.h^-1', 'km.h^-1', 1 ],
            [ 'km.h^-1', 'm.s^-1', 0.27777777777778 ],
            [ 'm.s^-1', 'km.h^-1', 3.6 ],
            [ 'm^2.animal^-1.m^-2.g.m^2.j^-5', 'animal^-1.g.m^2.j^-5', 1 ],
            [ 'm.m^-2.m^2', 'm', 1 ],
            [ 'kg^2.g', 'kg^3', 0.001 ],
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
