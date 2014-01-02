<?php

namespace MyCLabs\UnitBundle\Service\Operation;

use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitAPI\Operation\Operation;

/**
 * Executes an operation.
 *
 * @author matthieu.napoli
 */
interface OperationExecutor
{
    /**
     * Returns true if the executor can handle the given operation, false otherwise.
     *
     * @param Operation $operation
     *
     * @return boolean
     */
    public function handles(Operation $operation);

    /**
     * Executes a operation operation of units.
     *
     * @param Operation $operation
     *
     * @throws UnknownUnitException One of the unit is unknown.
     * @return string Resulting unit.
     */
    public function execute(Operation $operation);
}
