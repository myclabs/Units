<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

use MyCLabs\UnitBundle\Entity\IncompatibleUnitsException;

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
     */
    public function __construct(array $components)
    {
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
     *
     * @todo Refactor
     */
    public function getSymbol()
    {
        $leftPart = '';
        $rightPart = '';

        foreach ($this->components as $component) {
            // Pour les exposants positifs on construit le numérateur du symbole de l'unité.
            if ($component->getExponent() > 0) {
                $leftPart .= $component->getUnit()->getSymbol();
                if ($component->getExponent() > 1) {
                    switch ($component->getExponent()) {
                        case 2:
                            $leftPart .= '²';
                            break;
                        case 3:
                            $leftPart .= '³';
                            break;
                        default:
                            $leftPart .= $component->getExponent();
                    }
                }
                $leftPart .= '.';
            } elseif ($component->getExponent() < 0) {
                // Pour les exposants négatifs on construite le dénominateur du symbole de l'unité.
                $rightPart .= $component->getUnit()->getSymbol();
                if ($component->getExponent() < -1) {
                    // pour un exposant négatif on prend la valeur absolue de celui ci.
                    switch (abs($component->getExponent())) {
                        case 2:
                            $rightPart .= '²';
                            break;
                        case 3:
                            $rightPart .= '³';
                            break;
                        default:
                            $rightPart .= $component->getExponent();
                    }
                }
                $rightPart .= '.';
            }
        }

        // On supprime le dernier point de séparation à la fin de chaques parties du symbole.
        // Dans le cas ou une des parties est une chaine vide, cela renvoi une chaine vide.
        $leftPart = substr($leftPart, 0, -1);
        $rightPart = substr($rightPart, 0, -1);

        $leftPart = ($leftPart != '') ? $leftPart : '1';

        // Si on a une partie négative on sépare le numérateur et le dénominateur avec un trait de fraction
        if ($rightPart != '') {
            return $leftPart . '/' . $rightPart;
        } else {
            // Sinon on ne retourne que la partie positive.
            return $leftPart;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfReference()
    {
        /** @var UnitComponent[] $uniqueComponents */
        $uniqueComponents = [];

        // Simplifies for components of same unit
        foreach ($this->components as $component) {
            $unit = $component->getUnit()->getUnitOfReference();

            if (isset($uniqueComponents[$unit->getId()])) {
                $newExponent = $uniqueComponents[$unit->getId()]->getExponent() + $component->getExponent();
                $uniqueComponents[$unit->getId()]->setExponent($newExponent);
            } else {
                $uniqueComponents[$unit->getId()] = new UnitComponent($unit, $component->getExponent());
            }
        }

        // If only one component is left, with exponent=1, then it's a standard unit
        if (count($uniqueComponents) === 1) {
            /** @var UnitComponent $component */
            $component = reset($uniqueComponents);
            if ($component->getExponent() === 1) {
                return $component->getUnit();
            }
        }

        // Sort components so that equivalent Composed units are easily comparable
        usort($uniqueComponents, function (UnitComponent $a, UnitComponent $b) {
            return strcmp($a->getUnit()->getId(), $b->getUnit()->getId());
        });

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
                if ($componentUnit instanceof StandardUnit) {
                    $factor *= pow($componentUnit->getMultiplier(), $component->getExponent());
                }
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

        return $this->getConversionFactor() / $unit->getConversionFactor();
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
     * Array cartesian product.
     *
     * Returns all possible combinations between items of several arrays.
     *
     * @see http://stackoverflow.com/questions/8567082/how-to-generate-in-php-all-combinations-of-items-in-multiple-arrays
     * @param array[] $arrays Array of arrays.
     * @return array Cartesian product.
     */
    public function arrayCartesianProduct($arrays)
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
}
