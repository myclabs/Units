<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use MyCLabs\UnitBundle\Entity\UnitSystem;

/**
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PopulateUnitSystem
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
        $xml->load(__DIR__ . '/../../Resources/data/unitSystem.xml');

        foreach ($xml->getElementsByTagName('unitSystem') as $xmlUnitSystem) {
            $this->parseUnitSystem($xmlUnitSystem);
        }
    }

    /**
     * @param DOMElement $element
     */
    protected function parseUnitSystem(DOMElement $element)
    {
        $nameNode = $element->getElementsByTagName('name')->item(0);

        // Default label
        $label = $nameNode->getElementsByTagName('en')->item(0)->nodeValue;

        $unitSystem = new UnitSystem($element->getAttribute('ref'), $label);

        $this->entityManager->persist($unitSystem);

        // Label translations
        foreach ($nameNode->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);

            if ($lang == '' || $value == '' || $lang == 'en') {
                continue;
            }

            $this->translationRepository->translate($unitSystem, 'label', $lang, $value);
        }
    }
}
