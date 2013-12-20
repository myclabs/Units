<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitAPI\UnitOperationService;
use Symfony\Component\HttpFoundation\Response;

/**
 * REST controller for doing operations on units.
 */
class OperationController extends FOSRestController
{
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

        $unit1 = $this->getRequest()->get('unit1');
        if ($unit1 === null) {
            return new Response("This HTTP method expects a 'unit1' parameter", 400);
        }

        $unit2 = $this->getRequest()->get('unit2');
        if ($unit2 === null) {
            return new Response("This HTTP method expects a 'unit2' parameter", 400);
        }

        try {
            $compatible = (boolean) $operationService->areCompatible($unit1, $unit2);
        } catch (UnknownUnitException $e) {
            return new Response('UnknownUnitException: ' . $e->getMessage(), 404);
        }

        return $this->handleView($this->view($compatible, 200));
    }

    /**
     * @Get("/inverse/{unit}")
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
