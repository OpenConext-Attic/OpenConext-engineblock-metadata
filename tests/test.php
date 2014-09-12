<?php

require __DIR__ . '/../../../autoload.php';

$diContainer = new EngineBlock_Application_DiContainer();

$repo = \OpenConext\Component\EngineBlockMetadata\Entity\JanusMetadataRepository::createFromConfig(
    array(
        'dsn' => 'mysql://root:c0n3xt@localhost:3306/serviceregistry',
    ),
    $diContainer
);

var_dump($repo->fetchAllEntities());