<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use EngineBlock_Application_DiContainer;
use RuntimeException;
use Janus_Client;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\Assembler\JanusRestV1Assembler;

/**
 * Class JanusRestV1MetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class JanusRestV1MetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var Janus_Client
     */
    private $client;

    /**
     * @var JanusRestV1Assembler
     */
    private $translator;

    /**
     * @var RuntimeException
     */
    private $prevClientException = null;

    private $entityCache = array();

    /**
     * @param \Janus_Client_CacheProxy $client
     * @param JanusRestV1Assembler $translator
     */
    public function __construct(\Janus_Client_CacheProxy $client, JanusRestV1Assembler $translator)
    {
        $this->client = $client;
        $this->translator = $translator;
    }

    /**
     * @param array $repositoryConfig
     * @param EngineBlock_Application_DiContainer $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, EngineBlock_Application_DiContainer $container)
    {
        return new static($container->getServiceRegistryClient(), new JanusRestV1Assembler());
    }

    /**
     *
     * @param string $entityId
     * @return AbstractRole
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity) {
            throw new EntityNotFoundException(
                "Unable to find entity for entityId '$entityId' ",
                null,
                $this->prevClientException
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
        $entity = $this->findServiceProviderByEntityId($entityId);

        if (!$entity) {
            throw new EntityNotFoundException(
                "Unable to find entity for entityId '$entityId' ",
                null,
                $this->prevClientException
            );
        }

        return $entity;
    }

    /**
     * @param $entityId
     * @return null|IdentityProvider|ServiceProvider
     * @throws EntityNotFoundException
     */
    public function fetchIdentityProviderByEntityId($entityId)
    {
        $entity = $this->findIdentityProviderByEntityId($entityId);

        if (!$entity) {
            throw new EntityNotFoundException(
                "Unable to find entity for entityId '$entityId' ",
                null,
                $this->prevClientException
            );
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return AbstractRole|null
     */
    public function findEntityByEntityId($entityId)
    {
        if (isset($this->entityCache[$entityId])) {
            return $this->applyFilters($this->entityCache[$entityId]);
        }

        $metadata = $this->loadEntitiesMetadataCache()->findMetadataByEntityId($entityId);
        if (!$metadata) {
            return null;
        }

        $entity = $this->translator->translate($entityId, $metadata);
        if (!$entity) {
            return null;
        }

        $this->entityCache[$entityId] = $entity;
        return $this->applyFilters($this->entityCache[$entityId]);
    }

    /**
     * @param string $entityId
     * @return null|AbstractRole|ServiceProvider
     * @throws EntityNotFoundException
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        if (isset($this->entityCache[$entityId])) {
            return $this->applyFilters($this->entityCache[$entityId]);
        }

        $metadata = $this->loadEntitiesMetadataCache()->findIdentityProviderMetadataByEntityId($entityId);
        if (empty($metadata)) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        $entity = $this->translator->translate($entityId, $metadata);
        if (!$entity) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        if (!$entity instanceof IdentityProvider) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        $this->entityCache[$entityId] = $entity;
        return $this->applyFilters($this->entityCache[$entityId]);
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        $metadata = $this->loadEntitiesMetadataCache()->findServiceProviderMetadataByEntityId($entityId);
        if (empty($metadata)) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        $entity = $this->translator->translate($entityId, $metadata);

        if (!$entity) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        if (!$entity instanceof ServiceProvider) {
            $this->entityCache[$entityId] = null;
            return $this->entityCache[$entityId];
        }

        $this->entityCache[$entityId] = $entity;

        return $this->applyFilters($this->entityCache[$entityId]);
    }

    /**
     * @return array|IdentityProvider[]
     * @throws \RuntimeException
     */
    public function findIdentityProviders()
    {
        $entities = $this->loadEntitiesMetadataCache()->findIdentityProvidersMetadata();

        $identityProviders = array();
        foreach ($entities as $entityId => $entity) {
            if (!isset($this->entityCache[$entityId])) {
                $entity = $this->translator->translate($entityId, $entity);

                if (!is_null($entity) && !$entity instanceof IdentityProvider) {
                    throw new \RuntimeException('Service Registry returned a non-idp from getIdpList?');
                }

                $this->entityCache[$entityId] = $entity;
            }

            if (!$this->entityCache[$entityId]) {
                continue;
            }

            $entity = $this->applyFilters($this->entityCache[$entityId]);

            if (!$entity) {
                continue;
            }

            $identityProviders[$entityId] = $entity;
        }
        return $identityProviders;
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        $entityIds = $this->client->findIdentifiersByMetadata('coin:publish_in_edugain', 'yes');

        $publishable = array();
        foreach ($entityIds as $entityId) {
            $publishable[] = $this->fetchEntityByEntityId($entityId);
        }
        return $publishable;
    }

    /**
     * @param AbstractRole $entity
     * @return string
     * @throws EntityNotFoundException
     */
    public function fetchEntityManipulation(AbstractRole $entity)
    {
        $entityData = $this->fetchEntityDataForEntityId($entity->entityId);

        return $entityData['manipulation'];
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return AttributeReleasePolicy
     */
    public function fetchServiceProviderArp(ServiceProvider $serviceProvider)
    {
        $entityData = $this->fetchEntityDataForEntityId($serviceProvider->entityId);

        if ($entityData['arp'] === null) {
            return null;
        }

        return new AttributeReleasePolicy($entityData['arp']);
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return bool
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        static $allowedIdpsPerSp = array();

        if (!isset($allowedIdpsPerSp[$serviceProvider->entityId])) {
            $allowedIdpsPerSp[$serviceProvider->entityId] = $this->client->getAllowedIdps($serviceProvider->entityId);
        }

        return $allowedIdpsPerSp[$serviceProvider->entityId];
    }

    /**
     * @param string $entityId
     * @return mixed|null
     */
    private function fetchEntityDataForEntityId($entityId)
    {
        static $entities = array();

        if (!isset($entities[$entityId])) {
            $entities[$entityId] = $this->client->getEntity($entityId);
        }

        return $entities[$entityId];
    }

    /**
     * @return Helper\JanusRestV1Cache
     */
    private function loadEntitiesMetadataCache()
    {
        static $cache;

        if (!$cache) {
            $cache = new Helper\JanusRestV1Cache($this->client->getIdpList(), $this->client->getSpList());
        }

        return $cache;
    }
}
