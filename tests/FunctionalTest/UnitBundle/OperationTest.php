<?php

namespace FunctionalTest\UnitBundle;

use MyCLabs\UnitAPI\Operation\Addition;
use MyCLabs\UnitAPI\Operation\Multiplication;
use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitAPI\Operation\OperationBuilder;
use MyCLabs\UnitAPI\Operation\OperationComponent;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests the operations.
 *
 * @coversNothing
 */
class UnitOperationTest extends WebTestCase
{
    /**
     * @dataProvider operationProvider
     */
    public function testExecuteOperation(Operation $operation, $expected)
    {
        $client = static::createClient();

        switch ($operation) {
            case $operation instanceof Addition:
                $operationType = 'addition';
                break;
            case $operation instanceof Multiplication:
                $operationType = 'multiplication';
                break;
            default:
                throw new \Exception;
        }

        $components = array_map(function (OperationComponent $component) {
            return [
                'unit' => $component->getUnitId(),
                'exponent' => $component->getExponent(),
            ];
        }, $operation->getComponents());

        $query = http_build_query([
            'operation'  => $operationType,
            'components' => $components,
        ]);
        $client->request('GET', '/api/en/execute?' . $query);
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $this->assertEquals($expected, json_decode($response->getContent()));
    }

    public function operationProvider()
    {
        return [
            [
                OperationBuilder::addition()
                    ->with('m')
                    ->with('m')
                    ->getOperation(),
                'm'
            ],
            [
                OperationBuilder::addition()
                    ->with('m')
                    ->with('km')
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
            [
                OperationBuilder::addition()
                    ->with('m.s^-1', 2)
                    ->with('km.h^-1', 2)
                    ->getOperation(),
                'm^2.s^-2'
            ],
            [
                OperationBuilder::addition()
                    ->with('m/s')
                    ->with('km.h^-1')
                    ->getOperation(),
                'm.s^-1'
            ],
        ];
    }

    /**
     * @dataProvider conversionFactorProvider
     */
    public function testConversionFactor($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/en/conversion-factor?unit1=' . urlencode($unit1) . '&unit2=' . urlencode($unit2));
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

    public function testConversionFactorUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/conversion-factor?unit1=aaa&unit2=m');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('UnknownUnitException: Unknown unit aaa', $response->getContent());
    }

    public function testConversionFactorIncompatibleUnits()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/conversion-factor?unit1=m&unit2=g');
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $expected = 'IncompatibleUnitsException: Conversion factor impossible: units "m" and "g" have different physical quantities: "l" and "m"';
        $this->assertEquals($expected, $response->getContent());
    }

    /**
     * @dataProvider areCompatibleProvider
     */
    public function testAreCompatible($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/en/compatible?unit1=' . urlencode($unit1) . '&unit2=' . urlencode($unit2));
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
            [ 'm^2', 'm2', true ],
            [ 'm/s', 'm.s^-1', true ],
            [ 'm/s.h', 'm', true ],
        ];
    }

    public function testAreCompatibleUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/compatible?unit1=aaa&unit2=m');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('UnknownUnitException: Unknown unit aaa', $response->getContent());
    }

    /**
     * @dataProvider inverseProvider
     */
    public function testInverse($unit, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/en/inverse/' . urlencode($unit));
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $this->assertSame($expected, json_decode($response->getContent()));
    }

    public function inverseProvider()
    {
        return [
            'm'      => ['m', 'm^-1'],
            'm.h'    => ['m.h', 'm^-1.h^-1'],
            'm.h^-1' => ['m.h^-1', 'm^-1.h'],
            'm^2'    => ['m^2', 'm^-2'],
            'animal' => ['animal', 'animal^-1'],
        ];
    }

    public function testInverseUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/en/inverse/aaa');
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
        $this->assertEquals($statusCode, $response->getStatusCode(), substr($response->getContent(), 0, 500));
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
    }
}
