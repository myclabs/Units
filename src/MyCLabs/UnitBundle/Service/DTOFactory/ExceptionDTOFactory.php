<?php

namespace MyCLabs\UnitBundle\Service\DTOFactory;

use Exception;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;

/**
 * Creates a DTO for an exception.
 *
 * When returning an exception in the webservice, we don't want to show the stacktrace.
 * So we can't just show the exception object directly to the user, we have to use a DTO.
 *
 * @author matthieu.napoli
 */
class ExceptionDTOFactory
{
    /**
     * @param Exception $e
     * @return array
     */
    public function create(Exception $e)
    {
        $dto = [
            'message' => $e->getMessage(),
        ];

        if ($e instanceof UnknownUnitException) {
            $dto['unitId'] = $e->getUnitId();
        }

        return $dto;
    }
}
