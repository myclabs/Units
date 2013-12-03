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
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = (string) $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getId();
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


    private function build($id)
    {
        // TODO avec un vrai parser !


        // Tableau qui contiendra les références des unités.
        $componentRefs = [];

        // On parse chacun des caractères de la référence de l'unité composée.
        //  Pour chaque symbole, soit on termine la ref en cours et on l'ajoute au tableau,
        //  soit on continue de la construire.
        $splitRef = '';
        foreach (str_split($id, 1) as $symbol) {
            if ($symbol == '.' || $symbol == '^') {
                $componentRefs[] = $splitRef;
                $splitRef = '';
            }
            if ($symbol != '.') {
                $splitRef .= $symbol;
            }
        }
        if ($splitRef !== '') {
            $componentRefs[] = $splitRef;
        }

        // Tableau qui contiendra les symboles des unités chargés du model Unit.
        $this->components = [];

        // On parse chaque Ref composantes de l'unité composée.
        //  Pour chaque ref, on charge l'unité correspondante ou onr enseigne l'exposant.
        foreach ($componentRefs as $ref) {
            // Traitement des exposants.
            if (preg_match('#\^-?[0-9]+#', $ref)) {
                $exponent = preg_replace('#\^#', '', $ref);
            }
            // Traitement des unités.
            if (preg_match('#[a-zA-Z]+#', $ref)) {
                if (isset($unit)) {
                    if (!(isset($exponent))) {
                        $exponent = '1';
                    }
                    $this->components[] = new UnitComponent();
                }
                $unit = Unit::loadByRef($ref);
            }
        }
        if ($unit) {
            if (!(isset($exponent))) {
                $exponent = '1';
            }
            $this->components[] = new UnitComponent();
        }
    }
}
