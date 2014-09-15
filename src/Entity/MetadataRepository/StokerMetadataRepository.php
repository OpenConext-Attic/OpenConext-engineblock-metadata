<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\Translator\StokerTranslator;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataEntitySource;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataIndex;

class StokerMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var StokerTranslator
     */
    private $translator;

    /**
     * @var MetadataEntitySource
     */
    private $metadataEntitySource;

    /**
     * @var MetadataIndex
     */
    private $metadataIndex;

    /**
     * @param array $repositoryConfig
     * @param \EngineBlock_Application_DiContainer $container
     * @return mixed|static
     * @throws \RuntimeException
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container)
    {
        if (!isset($repositoryConfig['path'])) {
            throw new \RuntimeException('No path configured for stoker repository');
        }
        return new static($repositoryConfig['path'], new StokerTranslator());
    }

    /**
     * @param string $metadataDirectory
     * @param StokerTranslator $translator
     * @throws \RuntimeException
     */
    public function __construct($metadataDirectory, StokerTranslator $translator)
    {
        $this->metadataEntitySource = new MetadataEntitySource($metadataDirectory);
        $this->metadataIndex = MetadataIndex::load($metadataDirectory);
        if (!$this->metadataIndex) {
            throw new \RuntimeException(
                "Unable to load $metadataDirectory" . DIRECTORY_SEPARATOR . MetadataIndex::FILENAME
            );
        }
        $this->metadataDirectory = $metadataDirectory;
        $this->translator = $translator;
    }

    /**
     *
     * @param string $entityId
     * @return AbstractConfigurationEntity
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId)
    {
        $metadataIndexEntity = $this->metadataIndex->getEntityByEntityId($entityId);
        if (!$metadataIndexEntity) {
            throw new EntityNotFoundException("Unable to find entity in the index");
        }

        $xml = $this->metadataEntitySource->load($entityId);
        if (empty($xml)) {
            throw new EntityNotFoundException("Unable to find entity for '$entityId'");
        }

        $entity = $this->translator->translate($xml, $metadataIndexEntity);

        $entity = $this->applyFilters($entity);
        if (!$entity) {
            throw new EntityNotFoundException(
                "Found entity for '$entityId', but disallowed by filter: " . $this->getDisallowedByFilter()
            );
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return ServiceProviderEntity
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId)
    {
        $entity = $this->fetchEntityByEntityId($entityId);

        if (!$entity instanceof ServiceProviderEntity) {
            throw new EntityNotFoundException("Entity found for '$entityId' is not a Service Provider");
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity|null
     */
    public function findEntityByEntityId($entityId)
    {
        $metadataIndexEntity = $this->metadataIndex->getEntityByEntityId($entityId);
        if (!$metadataIndexEntity) {
            return null;
        }

        $xml = $this->metadataEntitySource->load($entityId);
        if (empty($xml)) {
            // @todo warn, the index has an entity that is not on disk?
            return null;
        }

        $entity = $this->translator->translate($xml, $metadataIndexEntity);

        $entity = $this->applyFilters($entity);
        if (!$entity) {
            return null;
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return ServiceProviderEntity|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity instanceof IdentityProviderEntity) {
            return null;
        }

        return $entity;
    }

    /**
     * @param $entityId
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity instanceof ServiceProviderEntity) {
            return null;
        }

        return $entity;
    }

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders()
    {
        $entities = $this->metadataIndex->getEntities();
        $identityProviders = array();
        foreach ($entities as $metadataIndexEntity) {
            if (!in_array(MetadataIndex\Entity::TYPE_IDP, $metadataIndexEntity->types)) {
                continue;
            }

            $entityXml = $this->metadataEntitySource->load($metadataIndexEntity->entityId);
            if (!$entityXml) {
                // @todo warn
                continue;
            }

            $entity = $this->translator->translate($entityXml, $metadataIndexEntity);

            $entity = $this->applyFilters($entity);
            if (!$entity) {
                // @todo warn
                continue;
            }

            $identityProviders[$entity->entityId] = $entity;
        }
        return $identityProviders;
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        return array();
    }
}