<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use MyCLabs\UnitBundle\Entity\TranslatedString;
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

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run()
    {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . '/../../Resources/data/unitSystem.xml');

        foreach ($xml->getElementsByTagName('unitSystem') as $xmlUnitSystem) {
            $this->parseUnitSystem($xmlUnitSystem);
        }
    }

    protected function parseUnitSystem(DOMElement $element)
    {
        $nameNode = $element->getElementsByTagName('name')->item(0);

        $label = new TranslatedString();
        foreach ($nameNode->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);

            if ($lang == '' || $value == '' || $lang == 'en') {
                continue;
            }

            $label->$lang = $value;
        }

        $unitSystem = new UnitSystem($element->getAttribute('ref'), $label);

        $this->entityManager->persist($unitSystem);
    }
}
