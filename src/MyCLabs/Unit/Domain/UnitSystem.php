<?php

namespace Unit\Domain;

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
    use Translatable;

    /**
     * @var int
     */
    protected $id;

    /**
     * Name of the unit system.
     *
     * @var string
     */
    protected $name;


    /**
     * @param string $name Name of the unit system.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name of the unit system.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
