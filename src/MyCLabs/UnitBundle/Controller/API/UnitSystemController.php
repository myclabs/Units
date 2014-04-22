<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\Controller\API\Helper\ExceptionHandlingHelper;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use MyCLabs\UnitBundle\Service\DTOFactory\UnitSystemDTOFactory;

/**
 * REST controller for unit systems.
 */
class UnitSystemController extends FOSRestController
{
    use ExceptionHandlingHelper;

    /**
     * @Inject
     * @var UnitSystemDTOFactory
     */
    private $dtoFactory;

    /**
     * @Get("/unit-system/")
     */
    public function getUnitSystemsAction()
    {
        $repository = $this->getDoctrine()->getRepository(UnitSystem::class);

        $unitSystems = $this->dtoFactory->createMany($repository->findAll());

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
            return $this->handleException(new \Exception("No unit system named $id was found"), 404);
        }

        $view = $this->view($this->dtoFactory->create($unitSystem), 200);

        return $this->handleView($view);
    }
}
