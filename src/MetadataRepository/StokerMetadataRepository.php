<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\Assembler\StokerAssembler;
use OpenConext\Component\StokerMetadata\MetadataEntitySource;
use OpenConext\Component\StokerMetadata\MetadataIndex;

/**
 * Class StokerMetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class StokerMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var StokerAssembler
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
     * @param ContainerInterface $container
     * @return mixed|static
     * @throws \RuntimeException
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container)
    {
        if (!isset($repositoryConfig['path'])) {
            throw new \RuntimeException('No path configured for stoker repository');
        }
        return new static($repositoryConfig['path'], new StokerAssembler());
    }

    /**
     * @param string $metadataDirectory
     * @param \OpenConext\Component\EngineBlockMetadata\Entity\Assembler\StokerAssembler $translator
     * @throws \RuntimeException
     */
    public function __construct($metadataDirectory, StokerAssembler $translator)
    {
        parent::__construct();

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
     * @return AbstractRole
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

        $entity = $this->filterCollection->filterRole($entity);
        if (!$entity) {
            throw new EntityNotFoundException(
                "Found entity for '$entityId', but disallowed by filter: " .
                $this->filterCollection->getDisallowedByFilter()
            );
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId)
    {
        $entity = $this->fetchEntityByEntityId($entityId);

        if (!$entity instanceof ServiceProvider) {
            throw new EntityNotFoundException("Entity found for '$entityId' is not a Service Provider");
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return AbstractRole|null
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

        $entity = $this->filterCollection->filterRole($entity);
        if (!$entity) {
            return null;
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity instanceof IdentityProvider) {
            return null;
        }

        return $entity;
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity instanceof ServiceProvider) {
            return null;
        }

        return $entity;
    }

    /**
     * @return IdentityProvider[]
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

            $entity = $this->filterCollection->filterRole($entity);
            if (!$entity) {
                // @todo warn
                continue;
            }

            $identityProviders[$entity->entityId] = $entity;
        }
        return $identityProviders;
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        return array();
    }
}
