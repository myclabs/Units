<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\DTO\UnitSystemDTO;
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

        $unitSystems = UnitSystemDTO::createMany($repository->findAll());

        $view = $this->view($unitSystems, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/unit-system/{id}/")
     */
    public function getUnitSystemAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);

        $unitSystem = $repository->find($id);

        if ($unitSystem === null) {
            throw new HttpException(404, "No unit system named $id was found");
        }

        $view = $this->view(UnitSystemDTO::create($unitSystem), 200);

        return $this->handleView($view);
    }
}
