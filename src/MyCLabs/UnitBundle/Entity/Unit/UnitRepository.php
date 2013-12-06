<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use Doctrine\Common\Persistence\ObjectRepository;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;

/**
 * Unit repository.
 *
 * @author matthieu.napoli
 */
interface UnitRepository extends ObjectRepository
{
    /**
     * Finds a unit by its identifier.
     *
     * @param string $id
     *
     * @throws UnknownUnitException
     * @return Unit
     */
    public function find($id);
}
