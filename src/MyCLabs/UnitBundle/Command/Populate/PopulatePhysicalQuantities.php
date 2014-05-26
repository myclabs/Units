<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\BasePhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\DerivedPhysicalQuantity;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\TranslatedString;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;

/**
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PopulatePhysicalQuantities
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run()
    {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . '/../../Resources/data/quantities.xml');

        foreach ($xml->getElementsByTagName('quantity') as $xmlPhysicalQuantity) {
            $this->parsePhysicalQuantity($xmlPhysicalQuantity);
        }
    }

    protected function parsePhysicalQuantity(DOMElement $element)
    {
        $label = new TranslatedString();
        foreach ($element->getElementsByTagName('name')->item(0)->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);
            if ($lang == '' || $value == '') {
                continue;
            }

            $label->$lang = $value;
        }

        if ($element->getElementsByTagName('symbol')->item(0)->hasChildNodes()) {
            $symbol = $element->getElementsByTagName('symbol')->item(0)->firstChild->nodeValue;
        } else {
            $symbol = '';
        }

        if ($element->getElementsByTagName('isBase')->item(0)->firstChild->nodeValue === 'true') {
            $physicalQuantity = new BasePhysicalQuantity($element->getAttribute('ref'), $label, $symbol);
        } else {
            $physicalQuantity = new DerivedPhysicalQuantity($element->getAttribute('ref'), $label, $symbol);
        }

        $this->entityManager->persist($physicalQuantity);
    }

    public function update()
    {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . '/../../Resources/data/quantities.xml');

        $quantities = $xml->getElementsByTagName("quantity");

        foreach ($quantities as $quantity) {
            $this->updateParserQuantity($quantity);
        }
    }

    protected function updateParserQuantity(DOMElement $element)
    {
        /** @var PhysicalQuantity $physicalQuantity */
        $physicalQuantity = $this->entityManager->find(PhysicalQuantity::class, $element->getAttribute('ref'));

        $unitRef = $element->getElementsByTagName('standardUnitRef')->item(0)->firstChild->nodeValue;

        /** @var StandardUnit $unit */
        $unit = $this->entityManager->find(StandardUnit::class, $unitRef);
        $physicalQuantity->setUnitOfReference($unit);

        if ($physicalQuantity instanceof DerivedPhysicalQuantity) {
            /** @var DerivedPhysicalQuantity $physicalQuantity */
            foreach ($element->getElementsByTagName('component') as $component) {
                $basePhysicalQuantityRef = $component->getElementsByTagName('baseQuantityRef')
                    ->item(0)->firstChild->nodeValue;

                /** @var BasePhysicalQuantity $baseQuantity */
                $baseQuantity = $this->entityManager->find(BasePhysicalQuantity::class, $basePhysicalQuantityRef);
                $exponent = $component->getElementsByTagName('exponent')->item(0)->firstChild->nodeValue;

                $physicalQuantity->addComponent($baseQuantity, $exponent);
            }
        }
    }
}
