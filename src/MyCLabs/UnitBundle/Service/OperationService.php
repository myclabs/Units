<?php

namespace MyCLabs\UnitBundle\Service;

use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException as DomainIncompatibleUnitsException;
use MyCLabs\UnitAPI\Exception\IncompatibleUnitsException as APIIncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\Unit\Unit;

/**
 * Service that converts values from a unit to another.
 */
class OperationService implements \MyCLabs\UnitAPI\OperationService
{
    /**
     * @var UnitExpressionParser
     */
    private $unitExpressionParser;

    public function __construct(UnitExpressionParser $unitExpressionParser)
    {
        $this->unitExpressionParser = $unitExpressionParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor($unit1, $unit2)
    {
        // Quick return if same units (no conversion)
        if ($unit1 == $unit2) {
            return 1;
        }

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
}
