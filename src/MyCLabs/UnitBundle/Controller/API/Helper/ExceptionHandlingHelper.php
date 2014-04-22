<?php

namespace MyCLabs\UnitBundle\Controller\API\Helper;

use Exception;

/**
 * Helper to handle exceptions in a REST controller.
 *
 * @author matthieu.napoli
 */
trait ExceptionHandlingHelper
{
    /**
     * @Inject
     * @var \MyCLabs\UnitBundle\Service\DTOFactory\ExceptionDTOFactory
     */
    private $exceptionDTOFactory;

    protected function handleException(Exception $e, $code)
    {
        $dto = $this->exceptionDTOFactory->create($e);

        return $this->handleView($this->view($dto, $code));
    }
}
