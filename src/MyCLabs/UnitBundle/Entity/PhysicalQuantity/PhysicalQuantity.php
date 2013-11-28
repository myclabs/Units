<?php

namespace MyCLabs\UnitBundle\Entity\PhysicalQuantity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;

/**
 * Physical quantity.
 *
 * @author valentin.claras
 * @author hugo.charbonnier
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PhysicalQuantity
{
    /**
     * Identifier.
     * @var string
     */
    protected $id;

    /**
     * Label.
     * @var string
     */
    protected $label;

    /**
     * Symbol.
     * @var string
     */
    protected $symbol;

    /**
     * Permet de savoir s'il s'agit d'une grandeur physique de base
     * si c'est à true, ou d'une grandeur physique dérivee si c'est à false.
     * @var bool
     */
    protected $isBase = true;

    /**
     * Unité de référence de la grandeur physique.
     * @var StandardUnit
     */
    protected $referenceUnit = null;

    /**
     * Permet de connaitre la composition d'une grandeur physique derivee en grandeur physique de base.
     * @var Collection|Component[]
     */
    protected $physicalQuantityComponents;

    /**
     * Locale for Translatable extension.
     * @var string
     */
    protected $translatableLocale;


    public function __construct($id)
    {
        $this->id = $id;

        $this->physicalQuantityComponents = new ArrayCollection();
    }

    /**
     * Défini le nom de la grandeur physique.
     * @param string $name
     */
    public function setLabel($name)
    {
        $this->label = $name;
    }

    /**
     * Renvoi le nom de la grandeur textuel.
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Défini le symbole de la grandeur physique.
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * Renvoi le symbole de la grandeur textuel.
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Défini si la grandeur physique est une base ou une dérivée.
     * @param bool $isBase
     */
    public function setIsBase($isBase)
    {
        $this->isBase = $isBase;
    }

    /**
     * Informe si la grandeur physique est une base ou non.
     * @return boolean
     */
    public function isBase()
    {
        return $this->isBase;
    }

    /**
     * Change l'unité de référence de la Grandeur Physique.
     * @param StandardUnit $unit La nouvelle unité de la GrandeurPhysiqueBase.
     */
    public function setReferenceUnit(StandardUnit $unit = null)
    {
        $this->referenceUnit = $unit;
    }

    /**
     * Retourne l'unite de réference associée à la Grandeur Physique.
     * @return StandardUnit
     */
    public function getReferenceUnit()
    {
        return $this->referenceUnit;
    }

    /**
     * @param PhysicalQuantity $basePhysicalQuantity
     * @param int              $exponent
     * @throws InvalidArgumentException
     */
    public function addPhysicalQuantityComponent(PhysicalQuantity $basePhysicalQuantity, $exponent)
    {
        if ($basePhysicalQuantity->isBase() === false) {
            throw new InvalidArgumentException('Only Base PhysicalQuantity can be added as Component');
        }
        $physicalQuantityComponent = new Component($this, $basePhysicalQuantity, $exponent);
        $this->physicalQuantityComponents->add($physicalQuantityComponent);
    }

    /**
     * Récupère la composition en grandeurs physiques de base d'une grandeur physique
     * @return PhysicalQuantity[]
     */
    public function getPhysicalQuantityComponents()
    {
        return $this->physicalQuantityComponents->toArray();
    }
}
