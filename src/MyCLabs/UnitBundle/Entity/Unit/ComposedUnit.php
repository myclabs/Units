<?php

namespace MyCLabs\UnitBundle\Entity\Unit;

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
        return $this->getSymbol();
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
        $leftPart = '';
        $rightPart = '';

        foreach ($this->components as $component) {
            // Pour les exposants positifs on construit le numérateur du symbole de l'unité.
            if ($component->getExponent() > 0) {
                $leftPart .= $component->getUnit()->getSymbol();
                if ($component->getExponent() > 1) {
                    $leftPart .= $component->getExponent();
                }
                $leftPart .= '.';
            } elseif ($component->getExponent() < 0) {
                // Pour les exposants négatifs on construite le dénominateur du symbole de l'unité.
                $rightPart .= $component->getUnit()->getSymbol();
                if ($component->getExponent() < -1) {
                    // pour un exposant négatif on prend la valeur absolue de celui ci.
                    $rightPart .= abs($component->getExponent());
                }
                $rightPart .= '.';
            }
        }

        // On supprime le dernier point de séparation à la fin de chaques parties du symbole.
        // Dans le cas ou une des parties est une chaine vide, cela renvoi une chaine vide.
        $leftPart = substr($leftPart, 0, -1);
        $rightPart = substr($rightPart, 0, -1);

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
    public function getReferenceUnit()
    {
        // TODO: Implement getReferenceUnit() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getConversionFactor(Unit $unit)
    {
        // TODO: Implement getConversionFactor() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getCompatibleUnits()
    {
        // TODO: Implement getCompatibleUnits() method.
    }
}
