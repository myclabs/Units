<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\TranslatedString;

/**
 * The empty unit (i.e. "no unit").
 *
 * @author matthieu.napoli
 */
class PercentUnit extends Unit
{
    const ID = 'pourcent';

    public function __construct()
    {
        $label = new TranslatedString();
        $label->en = 'percent';
        $label->fr = 'pour cent';

        parent::__construct(self::ID, $label, TranslatedString::untranslated('%'));
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfReference()
    {
        return new EmptyUnit();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        return new EmptyUnit();
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit = null)
    {
        if ($unit === null) {
            return 0.01;
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
            new EmptyUnit(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function pow($exponent)
    {
        return new ComposedUnit([ new UnitComponent($this, $exponent) ]);
    }
}
