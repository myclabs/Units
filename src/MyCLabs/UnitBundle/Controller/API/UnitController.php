<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\DTO\UnitDTO;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * REST controller for units.
 */
class UnitController extends FOSRestController
{
    /**
     * @Get("/unit/")
     */
    public function getUnitsAction()
    {
        $repository = $this->getDoctrine()->getRepository(Unit::class);
        $dtoFactory = $this->get('unit.dtoFactory.unit');

        $units = $dtoFactory->createMany($repository->findAll());

        $view = $this->view($units, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/unit/{id}/")
     */
    public function getUnitAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(Unit::class);
        $dtoFactory = $this->get('unit.dtoFactory.unit');

        $unit = $repository->find($id);

        if ($unit === null) {
            throw new HttpException(404, "No unit named $id was found");
        }

        $view = $this->view($dtoFactory->create($unit), 200);

        return $this->handleView($view);
    }
}
