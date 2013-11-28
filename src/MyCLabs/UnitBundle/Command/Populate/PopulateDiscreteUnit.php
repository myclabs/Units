<?php

namespace MyCLabs\UnitBundle\Command\Populate;

use Doctrine\ORM\EntityManager;
use DOMDocument;
use DOMElement;
use DOMNode;
use Unit\Domain\Unit\DiscreteUnit;

/**
 * @author hugo.charbonniere
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class PopulateDiscreteUnit
{
    public function run()
    {
        $xml = new DOMDocument();
        $xml->load(__DIR__ . '/../../Resources/data/discreteUnit.xml');

        foreach ($xml->getElementsByTagName('discreteUnit') as $discreteUnit) {
            $this->parseDiscreteUnit($discreteUnit);
        }
    }

    /**
     * @param DOMElement $element
     */
    private function parseDiscreteUnit(DOMElement $element)
    {
        $discreteUnit = new DiscreteUnit($element->getAttribute('ref'));

        foreach ($element->getElementsByTagName('name')->item(0)->childNodes as $node) {
            /** @var $node DOMNode */
            $lang = trim($node->nodeName);
            $value = trim($node->nodeValue);
            if ($lang == '' || $value == '') {
                continue;
            }

            $discreteUnit->setTranslatableLocale($lang);
            $discreteUnit->setName($value);
            $discreteUnit->save();
        }
    }
}
