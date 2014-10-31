<?php


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(
    array(__DIR__."/src"),
    $isDevMode,
    null,
    null,
    false
);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

Type::addType(
    'x509_certificates_collection',
    'OpenConext\Component\EngineBlockMetadata\Doctrine\Type\X509CertificatesCollectionType'
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
$conn = $entityManager->getConnection();
$conn->getDatabasePlatform()->registerDoctrineTypeMapping(
    'db_x509_certificates_collection',
    'x509_certificates_collection'
);
