<?php

namespace Unit\Domain\PhysicalQuantity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Unit\Domain\Unit\StandardUnit;
use Unit\Domain\Unit\Unit;

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
    use Translatable;

    /**
     * @var int
     */
    protected $id;

    /**
     * Name.
     * @var string
     */
    protected $name;

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


    public function __construct()
    {
        $this->physicalQuantityComponents = new ArrayCollection();
    }

    /**
     * Fonction appelée avant un delete (spécifié dans le mapper).
     *
     * @return void
     */
    public function preDelete()
    {
        // TODO remplacer par un cascade ?
        $this->deletePhysicalQuantityComponents();
    }

    /**
     * Supprime les physicalQuantityComponent
     *
     * @return void
     */
    protected function deletePhysicalQuantityComponents()
    {
        foreach ($this->physicalQuantityComponents as $physicalQuantityComponent) {
            $physicalQuantityComponent->delete();
        }
    }

    /**
     * Défini le nom de la grandeur physique.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Renvoi le nom de la grandeur textuel.
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Ajoute une ligne au tableau  $_composedPhysicalQuantities
     * @param PhysicalQuantity $basePhysicalQuantity
     * @param int              $exponent
     * @throws \Core_Exception_NotFound
     * @throws \Core_Exception_InvalidArgument
     */
    public function addPhysicalQuantityComponent(PhysicalQuantity $basePhysicalQuantity, $exponent)
    {
        if ($this->getKey() === array()) {
            throw new \Core_Exception_NotFound('PhysicalQuantity must be flushed before a Component can be added');
        }
        if ($basePhysicalQuantity->isBase() === false) {
            throw new \Core_Exception_InvalidArgument('Only Base PhysicalQuantity can be added as Component');
        }
        $physicalQuantityComponent = new Component();
        $physicalQuantityComponent->setDerivedPhysicalQuantity($this);
        $physicalQuantityComponent->setBasePhysicalQuantity($basePhysicalQuantity);
        $physicalQuantityComponent->setExponent($exponent);
        self::getEntityManager()->persist($physicalQuantityComponent);
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

    /**
     * @todo Transformer ça en association dans le modèle
     * @return Unit[]
     */
    public function getUnits()
    {
        $query = new \Core_Model_Query();
        $query->filter->addCondition(StandardUnit::QUERY_PHYSICALQUANTITY, $this);

        return StandardUnit::loadList($query);
    }
}
