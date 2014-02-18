<?php

namespace MyCLabs\UnitBundle\Controller\API\Helper;

use Exception;
use MyCLabs\UnitBundle\Service\DTOFactory\ExceptionDTOFactory;

/**
 * Helper to handle exceptions in a REST controller.
 *
 * @author matthieu.napoli
 */
trait ExceptionHandlingHelper
{
    protected function handleException(Exception $e, $code)
    {
        /** @var ExceptionDTOFactory $dtoFactory */
        $dtoFactory = $this->get('unit.dtoFactory.exception');

        $dto = $dtoFactory->create($e);

        return $this->handleView($this->view($dto, $code));
    }
}
