<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use Doctrine\Common\Persistence\ObjectRepository;

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
     * @return Unit
     */
    public function find($id);
}
