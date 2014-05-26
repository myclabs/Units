<?php

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

return [
    // Aliases to Symfony's container entries
    LoggerInterface::class => DI\link('logger'),
    EntityManager::class => DI\link('doctrine.orm.entity_manager'),
    TranslatorInterface::class => DI\link('translator'),
];
