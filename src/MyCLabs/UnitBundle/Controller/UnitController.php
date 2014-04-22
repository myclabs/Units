<?php

namespace MyCLabs\UnitBundle\Controller;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitAPI\UnitOperationService;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class UnitController extends Controller
{
    /**
     * @Inject
     * @var UnitOperationService
     */
    private $unitOperationService;

    /**
     * @Inject("session")
     * @var Session
     */
    private $session;

    /**
     * @Inject
     * @var TranslatorInterface
     */
    private $translator;

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

    /**
     * @Template
     */
    public function conversionAction(Request $request)
    {
        if ($request->get('unit1')) {
            $unit1 = $request->get('unit1');
            $unit2 = $request->get('unit2');

            try {
                $factor = $this->unitOperationService->getConversionFactor($unit1, $unit2);
            } catch (UnknownUnitException $e) {
                $this->session->getFlashBag()->add(
                    'error',
                    $this->translator->trans('unknownUnit', ['%id%' => $e->getUnitId()])
                );
                $factor = null;
            } catch (IncompatibleUnitsException $e) {
                $this->session->getFlashBag()->add(
                    'error',
                    $this->translator->trans('incompatibleUnits')
                );
                $factor = null;
            }

            return [
                'unit1' => $unit1,
                'unit2' => $unit2,
                'factor' => $factor,
            ];
        }

        return [];
    }
}
