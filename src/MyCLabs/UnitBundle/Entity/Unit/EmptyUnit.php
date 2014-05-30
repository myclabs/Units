<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\TranslatedString;

/**
 * The empty unit (i.e. "no unit").
 *
 * @author matthieu.napoli
 */
class EmptyUnit extends Unit
{
    const ID = 'un';

    public function __construct()
    {
        parent::__construct(self::ID, new TranslatedString(), new TranslatedString());
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfReference()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit = null)
    {
        if ($unit === null) {
            return 1.;
        }

        if (! $this->isCompatibleWith($unit)) {
            throw new IncompatibleUnitsException(sprintf(
                'Units "%s" and "%s" are not compatible',
                $this->getId(),
                $unit->getId()
            ));
        }

        if ($unit instanceof self) {
            return 1.;
        }

        return $unit->getConversionFactor() / $this->getConversionFactor();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleUnits()
    {
        return [
            new PercentUnit(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function pow($exponent)
    {
        return $this;
    }
}
