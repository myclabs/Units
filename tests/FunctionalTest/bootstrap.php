<?php

require_once __DIR__.'/../../app/bootstrap.php.cache';

$rootDir = __DIR__ . '/../..';

// Rebuild the database (SQLite database in tests/FunctionalTest/fixtures.db)
system("php $rootDir/app/console doctrine:schema:drop --force --env=test");
system("php $rootDir/app/console doctrine:schema:create --env=test");
system("php $rootDir/app/console unit:populate --env=test");
