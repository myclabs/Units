<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $operationService = $this->get('unit.service.operation');

        $unit1 = $this->getRequest()->get('unit1');
        if ($unit1 === null) {
            throw new HttpException(400, "This HTTP method expects a 'unit1' parameter");
        }

        $unit2 = $this->getRequest()->get('unit2');
        if ($unit2 === null) {
            throw new HttpException(400, "This HTTP method expects a 'unit2' parameter");
        }

        try {
            $conversionFactor = $operationService->getConversionFactor($unit1, $unit2);
        } catch (UnknownUnitException $e) {
            throw new HttpException(404, 'UnknownUnitException: ' . $e->getMessage());
        } catch (IncompatibleUnitsException $e) {
            throw new HttpException(400, 'IncompatibleUnitsException: ' . $e->getMessage());
        }

        return $this->handleView($this->view($conversionFactor, 200));
    }
}
