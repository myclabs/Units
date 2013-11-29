<?php

namespace MyCLabs\UnitBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * REST controller for unit systems.
 */
class UnitSystemRESTController extends FOSRestController
{
    /**
     * @Get("/unit-system/")
     */
    public function getUnitSystemsAction()
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);

        $unitSystems = $repository->findAll();

        $view = $this->view($unitSystems, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/unit-system/{id}/")
     */
    public function getUnitSystemAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);

        $unitSystems = $repository->find($id);

        if ($unitSystems === null) {
            throw new HttpException(404, "No unit system named $id was found");
        }

        $view = $this->view($unitSystems, 200);

        return $this->handleView($view);
    }
}
