<?php

namespace MyCLabs\UnitBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;

/**
 * Unit repository implementation using Doctrine
 */
class DoctrineUnitRepository extends EntityRepository implements UnitRepository
{
}
