<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use MyCLabs\UnitBundle\Entity\PhysicalQuantity\PhysicalQuantity;
use MyCLabs\UnitBundle\Entity\Unit\StandardUnit;
use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PopulateStandardUnit
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
        $xml->load(__DIR__ . '/../../Resources/data/standardUnit.xml');

        foreach ($xml->getElementsByTagName('standardUnit') as $standardUnit) {
            $this->parseStandardUnit($standardUnit);
        }
    }

    protected function parseStandardUnit(DOMElement $element)
    {
        $id = $element->getAttribute('ref');
        $multiplier = $element->getElementsByTagName('multiplier')->item(0)->firstChild->nodeValue;

        // Default label and symbol
        $label = $element->getElementsByTagName('name')->item(0)->getElementsByTagName('en')->item(0)->nodeValue;
        $symbol = $element->getElementsByTagName('symbol')->item(0)->getElementsByTagName('en')->item(0)->nodeValue;

        $idUnitSystem = $element->getElementsByTagName('unitSystemRef')->item(0)->firstChild->nodeValue;
        /** @var UnitSystem $unitSystem */
        $unitSystem = $this->entityManager->find(UnitSystem::class, $idUnitSystem);

        $idPhysicalQuantity = $element->getElementsByTagName('quantityRef')->item(0)->firstChild->nodeValue;
        /** @var PhysicalQuantity $physicalQuantity */
        $physicalQuantity = $this->entityManager->find(PhysicalQuantity::class, $idPhysicalQuantity);

        $unit = new StandardUnit($id, $label, $symbol, $physicalQuantity, $unitSystem, $multiplier);
        $physicalQuantity->addUnit($unit);

        $this->entityManager->persist($unit);

        // Label & Symbol
        foreach (['fr'] as $lang) {
            // Label
            $found = false;
            foreach ($element->getElementsByTagName('name')->item(0)->childNodes as $node) {
                if (trim($node->nodeName) == $lang) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "WARN: not found $lang traduction for name";
                continue;
            }
            /** @var $node DOMNode */
            $label = trim($node->nodeValue);

            // Symbol
            $found = false;
            foreach ($element->getElementsByTagName('symbol')->item(0)->childNodes as $node) {
                if (trim($node->nodeName) == $lang) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "WARN: not found $lang traduction for symbol";
                continue;
            }
            /** @var $node DOMNode */
            $symbol = trim($node->nodeValue);

            $this->translationRepository->translate($unit, 'label', $lang, $label);
            $this->translationRepository->translate($unit, 'symbol', $lang, $symbol);
        }
    }
}
