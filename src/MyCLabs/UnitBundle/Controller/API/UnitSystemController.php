<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\DTO\UnitSystemDTO;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * REST controller for unit systems.
 */
class UnitSystemController extends FOSRestController
{
    /**
     * @Get("/unit-system/")
     */
    public function getUnitSystemsAction()
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);
        $dtoFactory = $this->get('unit.dtoFactory.unitSystem');

        $unitSystems = $dtoFactory->createMany($repository->findAll());

        $view = $this->view($unitSystems, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/unit-system/{id}/")
     */
    public function getUnitSystemAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);
        $dtoFactory = $this->get('unit.dtoFactory.unitSystem');

        $unitSystem = $repository->find($id);

        if ($unitSystem === null) {
            throw new HttpException(404, "No unit system named $id was found");
        }

        $view = $this->view($dtoFactory->create($unitSystem), 200);

        return $this->handleView($view);
    }
}
