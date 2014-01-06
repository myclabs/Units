<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitAPI\Operation\Addition;
use MyCLabs\UnitAPI\Operation\Multiplication;
use MyCLabs\UnitAPI\Operation\OperationComponent;
use MyCLabs\UnitAPI\UnitOperationService;
use Symfony\Component\HttpFoundation\Response;

/**
 * REST controller for doing operations on units.
 */
class OperationController extends FOSRestController
{
    /**
     * @Get("/execute")
     */
    public function executeOperationAction()
    {
        /** @var UnitOperationService $operationService */
        $operationService = $this->get('unit.service.operation');

        $operationType = $this->getRequest()->get('operation');
        if ($operationType === null) {
            return new Response("This HTTP method expects an 'operation' parameter", 400);
        }

        $components = $this->getRequest()->get('components');
        if ($components === null || ! is_array($components)) {
            return new Response("This HTTP method expects a 'components' array parameter", 400);
        }
        $components = array_map(function ($array) {
            return new OperationComponent($array['unit'], $array['exponent']);
        }, $components);

        switch ($operationType) {
            case 'addition':
                $operation = new Addition($components);
                break;
            case 'multiplication':
                $operation = new Multiplication($components);
                break;
            default:
                return new Response("Invalid operation type: $operationType", 400);
        }

        try {
            $result = $operationService->execute($operation);
        } catch (UnknownUnitException $e) {
            return new Response('UnknownUnitException: ' . $e->getMessage(), 404);
        } catch (IncompatibleUnitsException $e) {
            return new Response('IncompatibleUnitsException: ' . $e->getMessage(), 400);
        }

        return $this->handleView($this->view($result, 200));
    }

    /**
     * @Get("/conversion-factor")
     */
    public function getConversionFactorAction()
    {
        /** @var UnitOperationService $operationService */
        $operationService = $this->get('unit.service.operation');

        $unit1 = $this->getRequest()->get('unit1');
        if ($unit1 === null) {
            return new Response("This HTTP method expects a 'unit1' parameter", 400);
        }

        $unit2 = $this->getRequest()->get('unit2');
        if ($unit2 === null) {
            return new Response("This HTTP method expects a 'unit2' parameter", 400);
        }

        try {
            $conversionFactor = $operationService->getConversionFactor($unit1, $unit2);
        } catch (UnknownUnitException $e) {
            return new Response('UnknownUnitException: ' . $e->getMessage(), 404);
        } catch (IncompatibleUnitsException $e) {
            return new Response('IncompatibleUnitsException: ' . $e->getMessage(), 400);
        }

        return $this->handleView($this->view($conversionFactor, 200));
    }

    /**
     * @Get("/compatible")
     */
    public function areCompatibleAction()
    {
        /** @var UnitOperationService $operationService */
        $operationService = $this->get('unit.service.operation');

        $units = $this->getRequest()->get('units');
        if ($units === null || ! is_array($units)) {
            return new Response("This HTTP method expects a 'units' array parameter", 400);
        }

        try {
            $compatible = (boolean) call_user_func_array([$operationService, 'areCompatible'], $units);
        } catch (UnknownUnitException $e) {
            return new Response('UnknownUnitException: ' . $e->getMessage(), 404);
        }

        return $this->handleView($this->view($compatible, 200));
    }

    /**
     * @Get("/inverse/{unit}", requirements={"unit"=".+"})
     */
    public function inverseAction($unit)
    {
        /** @var UnitOperationService $operationService */
        $operationService = $this->get('unit.service.operation');

        try {
            $inverse = $operationService->inverse($unit);
        } catch (UnknownUnitException $e) {
            return new Response('UnknownUnitException: ' . $e->getMessage(), 404);
        }

        return $this->handleView($this->view($inverse, 200));
    }
}
