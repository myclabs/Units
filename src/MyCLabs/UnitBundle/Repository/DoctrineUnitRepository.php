<?php

namespace MyCLabs\UnitBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Entity\Unit\EmptyUnit;
use MyCLabs\UnitBundle\Entity\Unit\PercentUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;

/**
 * Unit repository implementation using Doctrine
 */
class DoctrineUnitRepository extends EntityRepository implements UnitRepository
{
    public function findAll()
    {
        return array_merge(parent::findAll(), [
            new EmptyUnit(),
            new PercentUnit(),
        ]);
    }

    public function find($id)
    {
        if ($id === EmptyUnit::ID) {
            return new EmptyUnit();
        } elseif ($id === PercentUnit::ID) {
            return new PercentUnit();
        }

        $unit = parent::find($id);

        if ($unit === null) {
            throw UnknownUnitException::create($id);
        }

        return $unit;
    }
}
