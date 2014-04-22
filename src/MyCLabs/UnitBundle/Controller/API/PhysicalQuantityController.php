<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\Controller\API\Helper\ExceptionHandlingHelper;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Service\DTOFactory\PhysicalQuantityDTOFactory;

/**
 * REST controller for physical quantities.
 */
class PhysicalQuantityController extends FOSRestController
{
    use ExceptionHandlingHelper;

    /**
     * @Inject
     * @var PhysicalQuantityDTOFactory
     */
    private $dtoFactory;

    /**
     * @Get("/physical-quantity/")
     */
    public function getPhysicalQuantitiesAction()
    {
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);

        $physicalQuantities = $this->dtoFactory->createMany($repository->findAll());

        $view = $this->view($physicalQuantities, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/physical-quantity/{id}")
     */
    public function getPhysicalQuantityAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);

        $physicalQuantity = $repository->find($id);

        if ($physicalQuantity === null) {
            return $this->handleException(new \Exception("No physical quantity named $id was found"), 404);
        }

        $view = $this->view($this->dtoFactory->create($physicalQuantity), 200);

        return $this->handleView($view);
    }
}
