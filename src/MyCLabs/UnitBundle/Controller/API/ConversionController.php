<?php

namespace MyCLabs\UnitBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use MyCLabs\UnitAPI\Value;
use MyCLabs\UnitBundle\Service\ConversionService;

/**
 * REST controller for converting values between units.
 */
class ConversionController extends FOSRestController
{
    /**
     * @Post("/convert/")
     */
    public function convertAction()
    {
        /** @var ConversionService $conversionService */
        $conversionService = $this->get('unit.service.conversion');

        $value = Value::unserialize($this->getRequest()->get('value'));

        $targetUnit = $this->getRequest()->get('targetUnit');

        $newValue = $conversionService->convert($value, $targetUnit);

        return $this->handleView($this->view(['value' => $newValue->serialize()], 200));
    }
}
