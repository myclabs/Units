<?php

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use MyCLabs\UnitAPI\UnitOperationService;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;

return [
    UnitOperationService::class => DI\link(MyCLabs\UnitBundle\Service\UnitOperationService::class),

    UnitRepository::class => DI\factory(function (ContainerInterface $c) {
        /** @var EntityManager $entityManager */
        $entityManager = $c->get(EntityManager::class);
        return $entityManager->getRepository(Unit::class);
    }),
];
