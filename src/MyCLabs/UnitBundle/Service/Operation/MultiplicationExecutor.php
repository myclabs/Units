<?php

namespace MyCLabs\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Operation\Multiplication;
use MyCLabs\UnitAPI\Operation\Operation;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * Executes an operation of type Multiplication.
 *
 * @author matthieu.napoli
 */
class MultiplicationExecutor implements OperationExecutor
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
    public function handles(Operation $operation)
    {
        return $operation instanceof Multiplication;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Operation $operation)
    {
        if (! $this->handles($operation)) {
            throw new \InvalidArgumentException('Unhandled operation of type ' . get_class($operation));
        }

        throw new \Exception("TODO");
    }
}
