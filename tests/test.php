<?php

require __DIR__ . '/../../../autoload.php';

$diContainer = new EngineBlock_Application_DiContainer();

$repo = \OpenConext\Component\EngineBlockMetadata\Entity\JanusMetadataRepository::createFromConfig(
    array(
        'dsn' => 'mysq:host=root:c0n3xt@localhost;port=3306;dbname=serviceregistry',
    ),
    $diContainer
);

var_dump($repo->fetchAllEntities());