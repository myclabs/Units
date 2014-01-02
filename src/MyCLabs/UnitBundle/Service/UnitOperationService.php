<?php

namespace MyCLabs\UnitBundle\Service;

use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException as DomainIncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException as APIIncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Service\Operation\AdditionExecutor;
use MyCLabs\UnitBundle\Service\Operation\MultiplicationExecutor;
use MyCLabs\UnitBundle\Service\Operation\OperationExecutor;

/**
 * Service that performs operations on units.
 *
 * @author matthieu.napoli
 */
class UnitOperationService implements \MyCLabs\UnitAPI\UnitOperationService
{
    /**
     * @var UnitExpressionParser
     */
    private $unitExpressionParser;

    /**
     * @var OperationExecutor[]
     */
    private $operationExecutors;

    public function __construct(UnitExpressionParser $unitExpressionParser, array $operationExecutors = [])
    {
        $this->unitExpressionParser = $unitExpressionParser;
        $this->operationExecutors = $operationExecutors ?: [
            new AdditionExecutor($unitExpressionParser),
            new MultiplicationExecutor($unitExpressionParser),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Operation $operation)
    {
        foreach ($this->operationExecutors as $executor) {
            if ($executor->handles($operation)) {
                return $executor->execute($operation);
            }
        }

        throw new \InvalidArgumentException('Unhandled operation of type ' . get_class($operation));
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor($unit1, $unit2)
    {
        /** @var Unit $domainUnit1 */
        $domainUnit1 = $this->unitExpressionParser->parse($unit1);
        if ($domainUnit1 === null) {
            throw UnknownUnitException::create($unit1);
        }

        /** @var Unit $domainUnit2 */
        $domainUnit2 = $this->unitExpressionParser->parse($unit2);
        if ($domainUnit2 === null) {
            throw UnknownUnitException::create($unit2);
        }

        try {
            return $domainUnit1->getConversionFactor($domainUnit2);
        } catch (DomainIncompatibleUnitsException $e) {
            // Translate the domain exception into the API exception
            throw new APIIncompatibleUnitsException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function areCompatible($unit1, $unit2)
    {
        $units = func_get_args();

        $compatible = array_reduce($units, function (Unit $result = null, $unit) {
            $domainUnit = $this->unitExpressionParser->parse($unit);
            if ($domainUnit === null) {
                throw UnknownUnitException::create($unit);
            }

            // First iteration
            if ($result === null) {
                return $domainUnit;
            }

            if ($result->isCompatibleWith($domainUnit)) {
                // Compatible, we return a unit so that we can continue to compare against it
                return $result;
            } else {
                // Incompatible
                return false;
            }
        });

        return ($compatible !== false);
    }

    /**
     * {@inheritdoc}
     */
    public function multiply($unit1, $unit2)
    {
        // TODO: Implement multiply() method.
        throw new \Exception;
    }

    /**
     * {@inheritdoc}
     */
    public function inverse($unit)
    {
        $domainUnit = $this->unitExpressionParser->parse($unit);
        if ($domainUnit === null) {
            throw UnknownUnitException::create($unit);
        }

        $inverse = $domainUnit->inverse();

        return $inverse->getId();
    }
}
