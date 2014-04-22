<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Controller\API\Helper\ExceptionHandlingHelper;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Service\DTOFactory\UnitDTOFactory;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * REST controller for units.
 *
 * @author matthieu.napoli
 */
class UnitController extends FOSRestController
{
    use ExceptionHandlingHelper;

    /**
     * @Inject
     * @var UnitRepository
     */
    private $unitRepository;

    /**
     * @Inject
     * @var UnitDTOFactory
     */
    private $dtoFactory;

    /**
     * @Inject
     * @var UnitExpressionParser
     */
    private $parser;

    /**
     * @Get("/unit/")
     */
    public function getUnitsAction()
    {
        $units = $this->dtoFactory->createMany($this->unitRepository->findAll());

        return $this->handleView($this->view($units, 200));
    }

    /**
     * @Get("/unit/{expression}", requirements={"expression"=".+"})
     */
    public function getUnitAction($expression)
    {
        try {
            $unit = $this->parser->parse($expression);
        } catch (UnknownUnitException $e) {
            return $this->handleException($e, 404);
        }

        return $this->handleView($this->view($this->dtoFactory->create($unit), 200));
    }

    /**
     * @Get("/compatible-units/{expression}", requirements={"expression"=".+"})
     */
    public function getCompatibleUnitsAction($expression)
    {
        try {
            $unit = $this->parser->parse($expression);
        } catch (UnknownUnitException $e) {
            return $this->handleException($e, 404);
        }

        $units = $this->dtoFactory->createMany($unit->getCompatibleUnits());

        return $this->handleView($this->view($units, 200));
    }

    /**
     * @Get("/unit-of-reference/{expression}", requirements={"expression"=".+"})
     */
    public function getUnitOfReference($expression)
    {
        try {
            $unit = $this->parser->parse($expression);
        } catch (UnknownUnitException $e) {
            return $this->handleException($e, 404);
        }

        $unit = $this->dtoFactory->create($unit->getUnitOfReference());

        return $this->handleView($this->view($unit, 200));
    }
}
