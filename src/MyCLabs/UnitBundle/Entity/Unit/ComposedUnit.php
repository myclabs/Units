<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;
use MyCLabs\UnitBundle\Entity\TranslatedString;

/**
 * Unit composed of other units.
 *
 * @author valentin.claras
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class ComposedUnit extends Unit
{
    /**
     * @var UnitComponent[]
     */
    private $components = [];

    /**
     * @param UnitComponent[] $components
     * @throws \InvalidArgumentException Impossible to create a composed unit with no components
     */
    public function __construct(array $components)
    {
        if (empty($components)) {
            throw new \InvalidArgumentException('Impossible to create a composed unit with no components');
        }

        $this->components = $components;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $components = array_map(
            function (UnitComponent $component) {
                $exponent = ($component->getExponent() == 1) ? '' : '^' . $component->getExponent();
                return $component->getUnit()->getId() . $exponent;
            },
            $this->components
        );

        return implode('.', $components);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getSymbol();
    }

    /**
     * {@inheritdoc}
     */
    public function getSymbol()
    {
        $leftPart = [];
        $rightPart = [];

        foreach ($this->components as $component) {
            $unit = $component->getUnit();
            if ($unit instanceof EmptyUnit) {
                continue;
            }
            $exponent = $component->getExponent();

            if ($exponent > 0) {
                $leftPart[] = $unit->getSymbol();
                $leftPart[] = $this->getExponentSymbol($exponent);
                $leftPart[] = '.';
            } elseif ($exponent < 0) {
                $rightPart[] = $unit->getSymbol();
                // Pour un exposant négatif on prend la valeur absolue de celui ci.
                $rightPart[] = $this->getExponentSymbol(abs($exponent));
                $rightPart[] = '.';
            }
        }

        // Enlève le trailing "."
        array_pop($leftPart);
        array_pop($rightPart);

        if (empty($leftPart)) {
            if (empty($rightPart)) {
                return (new EmptyUnit())->getSymbol();
            }
            $leftPart = TranslatedString::untranslated('1');
        } else {
            $leftPart = TranslatedString::join($leftPart);
        }

        if (empty($rightPart)) {
            return $leftPart;
        }

        $rightPart = TranslatedString::join($rightPart);

        return TranslatedString::join([$leftPart, '/', $rightPart]);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Unit $unit)
    {
        if ($unit instanceof ComposedUnit) {
            $unit = $unit->simplify();
        }
        return ($this->simplify()->getId() === $unit->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfReference()
    {
        return $this->computeUnitOfReference(function (Unit $unit) {
            return $unit->getUnitOfReference();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUnitOfReference()
    {
        return $this->computeUnitOfReference(function (Unit $unit) {
            return $unit->getBaseUnitOfReference();
        });
    }

    /**
     * Computes the unit of reference, using the given function to find the unit of reference of a single unit.
     * @param callable $getUnitOfReference
     * @return Unit
     */
    private function computeUnitOfReference(callable $getUnitOfReference)
    {
        // Get units of references
        /** @var UnitComponent[] $components */
        $components = [];
        foreach ($this->components as $component) {
            $unit = $getUnitOfReference($component->getUnit());

            if ($unit instanceof ComposedUnit) {
                foreach ($unit->components as $composedUnitComponent) {
                    $newExponent = $composedUnitComponent->getExponent() * $component->getExponent();
                    $components[] = new UnitComponent($composedUnitComponent->getUnit(), $newExponent);
                }
            } else {
                $components[] = new UnitComponent($unit, $component->getExponent());
            }
        }

        $unitOfReference = new ComposedUnit($components);

        return $unitOfReference->simplify();
    }

    /**
     * Simplify the unit expression by merging components in the same unit.
     *
     * For example, the composed unit "m^2.m^-1" will return the standard unit "m".
     *
     * @return Unit
     */
    public function simplify()
    {
        /** @var UnitComponent[] $uniqueComponents */
        $uniqueComponents = [];
        foreach ($this->components as $component) {
            $unit = $component->getUnit();

            if (isset($uniqueComponents[$unit->getId()])) {
                $newExponent = $uniqueComponents[$unit->getId()]->getExponent() + $component->getExponent();
                $uniqueComponents[$unit->getId()]->setExponent($newExponent);
            } else {
                $uniqueComponents[$unit->getId()] = new UnitComponent($unit, $component->getExponent());
            }
        }

        // Sort components by the unit ID so that equivalent composed units are easily comparable
        ksort($uniqueComponents);

        // Remove components with exponent 0
        $uniqueComponents = array_filter($uniqueComponents, function (UnitComponent $component) {
            return $component->getExponent() != 0;
        });

        // Remove units without dimension (empty unit)
        unset($uniqueComponents[EmptyUnit::ID]);

        // If only one component is left, with exponent=1, then it's a standard unit
        if (count($uniqueComponents) === 1) {
            /** @var UnitComponent $component */
            $component = reset($uniqueComponents);
            if ($component->getExponent() === 1) {
                return $component->getUnit();
            }
        }

        if (empty($uniqueComponents)) {
            return new EmptyUnit();
        }

        return new ComposedUnit(array_values($uniqueComponents));
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit = null)
    {
        // Conversion to unit of reference
        if ($unit === null) {
            $factor = 1;

            foreach ($this->components as $component) {
                $componentUnit = $component->getUnit();
                $factor *= pow($componentUnit->getConversionFactor(), $component->getExponent());
            }

            return $factor;
        }

        if (! $this->isCompatibleWith($unit)) {
            throw new IncompatibleUnitsException(sprintf(
                'Units %s and %s are not compatible',
                $this->getId(),
                $unit->getId()
            ));
        }

        return $unit->getConversionFactor() / $this->getConversionFactor();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleUnits()
    {
        /*
         * For "m.s^-1"
         * Example: [
         *   0 => [ [m, 1], [km, 1] ],
         *   1 => [ [s, -1], [h, -1] ]
         * ]
         */
        $allComponentsPossible = [];

        // For each component, find alternative standard units
        foreach ($this->components as $component) {
            $compatibleUnits = array_map(
                function (Unit $unit) use ($component) {
                    return new UnitComponent($unit, $component->getExponent());
                },
                $component->getUnit()->getCompatibleUnits()
            );

            $allComponentsPossible[] = array_merge([$component], $compatibleUnits);
        }

        /*
         * Do a cartesian product of all combinations.
         *
         * Example: [
         *   0 => [ [m, 1], [s, -1] ],
         *   1 => [ [m, 1], [h, -1] ],
         *   2 => [ [km, 1], [s, -1] ],
         *   3 => [ [km, 1], [h, -1] ]
         * ]
         */
        $combinations = $this->arrayCartesianProduct($allComponentsPossible);

        // Removes the current unit (first item)
        unset($combinations[0]);

        // Turns each combination of components in a ComposedUnit
        return array_map(
            function ($components) {
                return new ComposedUnit($components);
            },
            $combinations
        );
    }

    /**
     * {@inheritdoc}
     */
    public function pow($exponent)
    {
        $components = array_map(
            function (UnitComponent $component) use ($exponent) {
                return new UnitComponent($component->getUnit(), $component->getExponent() * $exponent);
            },
            $this->components
        );

        return new ComposedUnit($components);
    }

    /**
     * @return UnitComponent[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * Array cartesian product.
     *
     * Returns all possible combinations between items of several arrays.
     *
     * @see http://stackoverflow.com/questions/8567082/how-to-generate-in-php-all-combinations-of-items-in-multiple-arrays
     * @param array[] $arrays Array of arrays.
     * @return array Cartesian product.
     */
    private function arrayCartesianProduct($arrays)
    {
        if (count($arrays) == 0) {
            return [[]];
        }

        $array = array_shift($arrays);
        $c = $this->arrayCartesianProduct($arrays);
        $return = [];

        foreach ($array as $v) {
            foreach ($c as $p) {
                $return[] = array_merge([$v], $p);
            }
        }

        return $return;
    }

    private function getExponentSymbol($exponent)
    {
        switch ($exponent) {
            case 1:
                return '';
                break;
            case 2:
                return '²';
                break;
            case 3:
                return '³';
                break;
            default:
                return (string) $exponent;
        }
    }
}
