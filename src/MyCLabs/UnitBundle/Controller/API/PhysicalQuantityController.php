<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use Symfony\Component\HttpFoundation\Response;

/**
 * REST controller for physical quantities.
 */
class PhysicalQuantityController extends FOSRestController
{
    /**
     * @Get("/physical-quantity/")
     */
    public function getPhysicalQuantitiesAction()
    {
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);
        $dtoFactory = $this->get('unit.dtoFactory.physicalQuantity');

        $physicalQuantities = $dtoFactory->createMany($repository->findAll());

        $view = $this->view($physicalQuantities, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/physical-quantity/{id}")
     */
    public function getPhysicalQuantityAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);
        $dtoFactory = $this->get('unit.dtoFactory.physicalQuantity');

        $physicalQuantity = $repository->find($id);

        if ($physicalQuantity === null) {
            return new Response("No physical quantity named $id was found", 404);
        }

        $view = $this->view($dtoFactory->create($physicalQuantity), 200);

        return $this->handleView($view);
    }
}
