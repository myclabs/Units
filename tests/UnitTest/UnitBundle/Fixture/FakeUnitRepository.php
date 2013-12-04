<?php

namespace UnitTest\UnitBundle\Fixture;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Entity\UnitSystem;

class FakeUnitRepository implements UnitRepository
{
    public function __construct()
    {
        $international = new UnitSystem('international', 'International');

        $length = new BasePhysicalQuantity('l', 'Length', 'L');
        $time = new BasePhysicalQuantity('t', 'Time', 'T');

        $this->units = [
            'm' => new StandardUnit('m', 'Meter', 'm', $length, $international, 1),
            'km' => new StandardUnit('km', 'KiloMeter', 'km', $length, $international, 1000),
            's' => new StandardUnit('s', 'Second', 's', $time, $international, 1),
            'h' => new StandardUnit('h', 'Hour', 'h', $time, $international, 3600),
        ];

        $length->setUnitOfReference($this->units['m']);
        $time->setUnitOfReference($this->units['s']);
    }

    public function findAll()
    {
        return $this->units;
    }

    public function find($id)
    {
        return $this->units[$id];
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        throw new \Exception("Not implemented");
    }

    public function findOneBy(array $criteria)
    {
        throw new \Exception("Not implemented");
    }

    public function getClassName()
    {
        throw new \Exception("Not implemented");
    }
}
