<?php

namespace UnitTest\UnitBundle\Fixture;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\DerivedPhysicalQuantity;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Entity\UnitSystem;

class FakeUnitRepository implements UnitRepository
{
    public function __construct()
    {
        $international = new UnitSystem('international', 'International');
        $anglosaxon = new UnitSystem('anglosaxon', 'Anglo-Saxon');

        $length = new BasePhysicalQuantity('l', 'L', 'Length');
        $time = new BasePhysicalQuantity('t', 'T', 'Time');
        $mass = new BasePhysicalQuantity('m', 'M', 'Mass');
        $speed = new DerivedPhysicalQuantity('s', 'S', 'Speed');
        $speed->addComponent($length, 1);
        $speed->addComponent($time, -1);

        $this->units = [
            'm' => new StandardUnit('m', 'meter', 'm', $length, $international, 1),
            'km' => new StandardUnit('km', 'kilometer', 'km', $length, $international, 1000),
            's' => new StandardUnit('s', 'second', 's', $time, $international, 1),
            'h' => new StandardUnit('h', 'hour', 'h', $time, $international, 3600),
            'kg' => new StandardUnit('kg', 'kilogram', 'kg', $mass, $international, 1),
            'm/s' => new StandardUnit('m/s', 'meter per second', 'm/s', $speed, $international, 1),
            'knot' => new StandardUnit('knot', 'knot', 'kt', $speed, $anglosaxon, 0.514),
        ];

        $length->setUnitOfReference($this->units['m']);
        $time->setUnitOfReference($this->units['s']);
        $mass->setUnitOfReference($this->units['kg']);
        $speed->setUnitOfReference($this->units['m/s']);
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
