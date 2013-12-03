<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitBundle\DTO\PhysicalQuantityDTO;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\UnitSystem;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        $physicalQuantities = PhysicalQuantityDTO::createMany($repository->findAll());

        $view = $this->view($physicalQuantities, 200);

        return $this->handleView($view);
    }

    /**
     * @Get("/physical-quantity/{id}/")
     */
    public function getPhysicalQuantityAction($id)
    {
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);

        $physicalQuantity = $repository->find($id);

        if ($physicalQuantity === null) {
            throw new HttpException(404, "No physical quantity named $id was found");
        }

        $view = $this->view(PhysicalQuantityDTO::create($physicalQuantity), 200);

        return $this->handleView($view);
    }
}
