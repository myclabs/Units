<?php

namespace MyCLabs\UnitBundle\Controller;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PhysicalQuantityController extends Controller
{
    /**
     * @Template
     */
    public function listAction()
    {
        /** @var EntityRepository $repository */
        $repository = $this->getDoctrine()->getRepository(PhysicalQuantity::class);

        return ['physicalQuantities' => $repository->findAll()];
    }
}
