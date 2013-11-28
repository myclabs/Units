<?php

namespace MyCLabs\UnitBundle\Entity;

use Exception;

/**
 * Units are incompatibles, there is no conversion factor between them.
 *
 * @author valentin.claras
 */
class IncompatibleUnitsException extends Exception
{
}
