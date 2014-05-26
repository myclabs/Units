<?php

namespace UnitTest\UnitBundle\Fixture;

use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\DerivedPhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Entity\UnitSystem;

class FakeUnitRepository implements UnitRepository
{
    public function __construct()
    {
        $international = new UnitSystem('international', new TranslatedString());
        $anglosaxon = new UnitSystem('anglosaxon', new TranslatedString());

        $length = new BasePhysicalQuantity('l', new TranslatedString('L', 'en'), 'Length');
        $time = new BasePhysicalQuantity('t', new TranslatedString('T', 'en'), 'Time');
        $mass = new BasePhysicalQuantity('m', new TranslatedString('M', 'en'), 'Mass');
        $speed = new DerivedPhysicalQuantity('s', new TranslatedString('S', 'en'), 'Speed');
        $speed->addComponent($length, 1);
        $speed->addComponent($time, -1);

        $this->units = [
            'm' => new StandardUnit('m', new TranslatedString(), new TranslatedString(), $length, $international, 1),
            'km' => new StandardUnit('km', new TranslatedString(), new TranslatedString(), $length, $international, 1000),
            's' => new StandardUnit('s', new TranslatedString(), new TranslatedString(), $time, $international, 1),
            'h' => new StandardUnit('h', new TranslatedString(), new TranslatedString(), $time, $international, 3600),
            'kg' => new StandardUnit('kg', new TranslatedString(), new TranslatedString(), $mass, $international, 1),
            'm/s' => new StandardUnit('m/s', new TranslatedString(), new TranslatedString(), $speed, $international, 1),
            'knot' => new StandardUnit('knot', new TranslatedString(), new TranslatedString(), $speed, $anglosaxon, 0.514),
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
