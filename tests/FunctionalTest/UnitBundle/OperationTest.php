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
    public function testExecuteOperation(Operation $operation, $expectedUnit, $expectedConversionFactor = null)
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
        $client->request('GET', '/api/execute?' . $query);
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $result = json_decode($response->getContent());
        $this->assertEquals($expectedUnit, $result->unitId);
        if ($operation instanceof Multiplication) {
            $this->assertEquals($expectedConversionFactor, $result->conversionFactor);
        }
    }

    public function operationProvider()
    {
        return [
            // Additions
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
            [
                OperationBuilder::addition()
                    ->with('un')
                    ->with('un')
                    ->getOperation(),
                'un'
            ],
            [
                OperationBuilder::addition()
                    ->with('pourcent')
                    ->with('un')
                    ->getOperation(),
                'un'
            ],
            // Multiplications
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
                    ->with('g', 1)
                    ->getOperation(),
                'kg.m',
                0.001
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', -3)
                    ->with('km', 2)
                    ->with('g', 1)
                    ->getOperation(),
                'kg.m^-1',
                1000.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('m', -1)
                    ->getOperation(),
                'un',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('m', 1)
                    ->with('km^2', -1)
                    ->getOperation(),
                'm^-1',
                1. / (1000. * 1000.)
            ],
            [
                OperationBuilder::multiplication()
                    ->with('un', 1)
                    ->with('un', 1)
                    ->getOperation(),
                'un',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('un', 1)
                    ->with('un', -1)
                    ->getOperation(),
                'un',
                1.
            ],
            [
                OperationBuilder::multiplication()
                    ->with('un', 1)
                    ->with('pourcent', 1)
                    ->getOperation(),
                'un',
                0.01
            ],
        ];
    }

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
            [ 'm', 'm', 1. ],
            [ 'm', 'km', 1000. ],
            [ 'km', 'm', 0.001 ],
            [ 'km.h^-1', 'km.h^-1', 1. ],
            [ 'km.h^-1', 'm.s^-1', 3.6 ],
            [ 'm.s^-1', 'km.h^-1', 0.27777777777778 ],
            [ 'm^2.animal^-1.m^-2.g.m^2.j^-5', 'animal^-1.g.m^2.j^-5', 1. ],
            [ 'm.m^-2.m^2', 'm', 1. ],
            [ 'kg^2.g', 'kg^3', 1000. ],
            [ 'm/s', 'km.h^-1', 0.27777777777778 ],
            [ 'un', 'un', 1. ],
            [ 'un', 'pourcent', 0.01 ],
            [ 'pourcent', 'un', 100. ],
            [ 'pourcent.un', 'un', 100. ],
            [ 'pourcent.m', 'm', 100. ],
        ];
    }

    public function testConversionFactorUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/conversion-factor?unit1=aaa&unit2=m');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('Unknown unit aaa', $exception->message);
        $this->assertEquals('aaa', $exception->unitId);
    }

    public function testConversionFactorIncompatibleUnits()
    {
        $client = static::createClient();
        $client->request('GET', '/api/conversion-factor?unit1=m&unit2=g');
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('Units "m" and "g" are not compatible', $exception->message);
    }

    /**
     * @dataProvider areCompatibleProvider
     */
    public function testAreCompatible($unit1, $unit2, $expected)
    {
        $client = static::createClient();

        $query = http_build_query([
            'units'  => [$unit1, $unit2],
        ]);

        $client->request('GET', '/api/compatible?' . $query);
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
            [ 'j', 'kwh', true ],
            [ 'm^2.kg.s^-2', 'j', true ],
            [ 'j', 'm^2.kg.s^-2', true ],
            [ 'un', 'un', true ],
            [ 'un', 'pourcent', true ],
            [ 'un', 'm', false ],
            [ 'un', 'm.m^-1', true ],
            [ 'un', 'j^-1.m^2.kg.s^-2', true ],
        ];
    }

    public function testAreCompatibleUnitNotFound()
    {
        $client = static::createClient();

        $query = http_build_query([
            'units'  => ['aaa', 'm'],
        ]);

        $client->request('GET', '/api/compatible?' . $query);
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('Unknown unit aaa', $exception->message);
        $this->assertEquals('aaa', $exception->unitId);
    }

    /**
     * @dataProvider inverseProvider
     */
    public function testInverse($unit, $expected)
    {
        $client = static::createClient();

        $client->request('GET', '/api/inverse/' . urlencode($unit));
        $response = $client->getResponse();

        $this->assertJsonResponse($response);

        $this->assertSame($expected, json_decode($response->getContent()));
    }

    public function inverseProvider()
    {
        return [
            'm'        => ['m', 'm^-1'],
            'm.h'      => ['m.h', 'm^-1.h^-1'],
            'm.h^-1'   => ['m.h^-1', 'm^-1.h'],
            'm^2'      => ['m^2', 'm^-2'],
            'animal'   => ['animal', 'animal^-1'],
            'm/s'      => ['m/s', 'm/s^-1'],
            'un'       => ['un', 'un'],
            'pourcent' => ['pourcent', 'pourcent^-1'],
            'un^-1'    => ['un^-1', 'un'],
        ];
    }

    public function testInverseUnitNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/api/inverse/aaa');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $exception = json_decode($response->getContent());
        $this->assertEquals('Unknown unit aaa', $exception->message);
        $this->assertEquals('aaa', $exception->unitId);
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
