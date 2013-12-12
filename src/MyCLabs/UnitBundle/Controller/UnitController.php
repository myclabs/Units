<?php

namespace MyCLabs\UnitBundle\Controller;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UnitController extends Controller
{
    /**
     * @Template
     */
    public function listAction()
    {
        /** @var EntityRepository $standardUnitRepository */
        $standardUnitRepository = $this->getDoctrine()->getRepository(StandardUnit::class);
        /** @var EntityRepository $discreteUnitRepository */
        $discreteUnitRepository = $this->getDoctrine()->getRepository(DiscreteUnit::class);

        $standardUnits = $standardUnitRepository->findBy([], [
            'physicalQuantity' => 'asc',
            'multiplier' => 'asc'
        ]);

        return [
            'standardUnits' => $standardUnits,
            'discreteUnits' => $discreteUnitRepository->findAll(),
        ];
    }
}
