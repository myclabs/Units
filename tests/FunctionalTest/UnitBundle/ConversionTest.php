<?php

namespace FunctionalTest\UnitBundle;

use MyCLabs\UnitAPI\Value;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the conversion API.
 */
class ConversionTest extends WebTestCase
{
    /**
     * @dataProvider scenarioProvider
     */
    public function testConvert(Value $value, $targetUnit, $targetNumericValue)
    {
        $client = static::createClient();

        $params = [
            'targetUnit' => $targetUnit,
            'value' => $value->serialize(),
        ];

        $client->request('POST', '/api/convert/', $params);

        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $content = json_decode($response->getContent());

        $this->assertEquals($targetNumericValue, $content->numeric_value);
        $this->assertEquals($targetUnit, $content->unit);
        $this->assertEquals($value->getUncertainty(), $content->uncertainty);
    }

    public function scenarioProvider()
    {
        return [
            [ new Value(10, 'km.h^-1', 5), 'km.h^-1', 10 ],
            [ new Value(10, 'km.h^-1', 5), 'm.s^-1', 2.7777777777778 ],
            [ new Value(10, 'm.s^-1', 5), 'km.h^-1', 36 ],
            [ new Value(1, 'm^2.animal^-1.m^-2.g.m^2.j^-5'), 'animal^-1.g.m^2.j^-5', 1 ],
            [ new Value(1, 'm.m^-2.m^2'), 'm', 1 ],
        ];
    }

    protected static function getPhpUnitXmlDir()
    {
        return parent::getPhpUnitXmlDir() . '/app';
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getContent());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
    }
}
