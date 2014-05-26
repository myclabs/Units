<?php

namespace MyCLabs\UnitBundle\Entity;

/**
 * Unit system.
 *
 * Examples: SI (international system of units, british imperial system)
 *
 * @author valentin.claras
 * @author hugo.charbonnier
 * @author yoann.croizer
 * @author matthieu.napoli
 */
class UnitSystem
{
    /**
     * Identifier for the system.
     *
     * @var string
     */
    protected $id;

    /**
     * Label of the unit system.
     *
     * @var TranslatedString
     */
    protected $label;

    /**
     * Locale for Translatable extension.
     *
     * @var string
     */
    protected $translatableLocale;


    /**
     * @param string           $id    Identifier for the system.
     * @param TranslatedString $label Label of the unit system.
     */
    public function __construct($id, TranslatedString $label)
    {
        $this->id = (string) $id;
        $this->label = $label;
    }

    /**
     * Returns the identifier for the system.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the name of the unit system.
     *
     * @return TranslatedString
     */
    public function getLabel()
    {
        return $this->label;
    }
}
