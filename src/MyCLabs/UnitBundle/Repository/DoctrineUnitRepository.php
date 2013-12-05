<?php

namespace MyCLabs\UnitBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;

/**
 * Unit repository implementation using Doctrine
 */
class DoctrineUnitRepository extends EntityRepository implements UnitRepository
{
    public function find($id)
    {
        $unit = parent::find($id);

        if ($unit === null) {
            throw new \InvalidArgumentException("No unit was found for id '$id'");
        }

        return $unit;
    }
}
