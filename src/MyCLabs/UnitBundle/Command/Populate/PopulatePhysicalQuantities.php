<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;

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

    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->translationRepository = $entityManager->getRepository(Translation::class);
    }

    public function run()
    {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . '/../../Resources/data/quantities.xml');

        foreach ($xml->getElementsByTagName('quantity') as $xmlPhysicalQuantity) {
            $this->parsePhysicalQuantity($xmlPhysicalQuantity);
        }
    }

    /**
     * @param DOMElement $element
     */
    protected function parsePhysicalQuantity(DOMElement $element)
    {
        $physicalQuantity = new PhysicalQuantity($element->getAttribute('ref'));

        if ($element->getElementsByTagName('symbol')->item(0)->hasChildNodes()) {
            $physicalQuantity->setSymbol($element->getElementsByTagName('symbol')->item(0)->firstChild->nodeValue);
        }
        if ($element->getElementsByTagName('isBase')->item(0)->firstChild->nodeValue === 'true') {
            $physicalQuantity->setIsBase(true);
        } else {
            $physicalQuantity->setIsBase(false);
        }

        $this->entityManager->persist($physicalQuantity);

        // Label
        foreach ($element->getElementsByTagName('name')->item(0)->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);
            if ($lang == '' || $value == '') {
                continue;
            }

            $this->translationRepository->translate($physicalQuantity, 'label', $lang, $value);
        }
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

    /**
     * @param DOMElement $element
     */
    protected function updateParserQuantity(DOMElement $element)
    {
        $physicalQuantity = PhysicalQuantity::loadByRef($element->getAttribute('ref'));

        $unitRef = $element->getElementsByTagName('standardUnitRef')->item(0)->firstChild->nodeValue;
        $unit = StandardUnit::loadByRef($unitRef);
        $physicalQuantity->setReferenceUnit($unit);

        if ($element->getElementsByTagName('isBase')->item(0)->firstChild->nodeValue === 'false') {
            foreach ($element->getElementsByTagName('component') as $component) {
                $basePhysicalQuantityRef = $component->getElementsByTagName('baseQuantityRef')->item(0)->firstChild->nodeValue;
                $basePhysicalQuantity = PhysicalQuantity::loadByRef($basePhysicalQuantityRef);
                $exponent = $component->getElementsByTagName('exponent')->item(0)->firstChild->nodeValue;
                $physicalQuantity->addPhysicalQuantityComponent($basePhysicalQuantity, $exponent);
            }
        } else {
            $physicalQuantity->addPhysicalQuantityComponent($physicalQuantity, 1);
        }
    }
}
