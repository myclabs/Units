<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use MyCLabs\UnitBundle\Entity\Unit\DiscreteUnit;

/**
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PopulateDiscreteUnit
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
        $xml->load(__DIR__ . '/../../Resources/data/discreteUnit.xml');

        foreach ($xml->getElementsByTagName('discreteUnit') as $discreteUnit) {
            $this->parseDiscreteUnit($discreteUnit);
        }
    }

    private function parseDiscreteUnit(DOMElement $element)
    {
        // Default label
        $label = $element->getElementsByTagName('name')->item(0)->getElementsByTagName('en')->item(0)->nodeValue;

        $unit = new DiscreteUnit($element->getAttribute('ref'), $label);

        $this->entityManager->persist($unit);

        foreach ($element->getElementsByTagName('name')->item(0)->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);

            if ($lang == '' || $value == '' || $lang == 'en') {
                continue;
            }

            $this->translationRepository->translate($unit, 'label', $lang, $value);
            $this->translationRepository->translate($unit, 'symbol', $lang, $value);
        }
    }
}
