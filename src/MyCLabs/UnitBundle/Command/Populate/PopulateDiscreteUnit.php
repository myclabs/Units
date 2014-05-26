<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Mnapoli\Translated\Translator;
use MyCLabs\UnitBundle\Entity\TranslatedString;
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

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

        $unit = new DiscreteUnit($element->getAttribute('ref'), $label);

        $this->entityManager->persist($unit);
    }
}
