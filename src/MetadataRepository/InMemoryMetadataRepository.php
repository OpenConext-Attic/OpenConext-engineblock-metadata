<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use InvalidArgumentException;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Class InMemoryMetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 */
class InMemoryMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var ServiceProvider[]
     */
    private $serviceProviders = array();

    /**
     * @var IdentityProvider[]
     */
    private $identityProviders = array();

    /**
     * @param IdentityProvider[] $identityProviders
     * @param ServiceProvider[] $serviceProviders
     * @throws InvalidArgumentException
     */
    public function __construct(array $identityProviders, array $serviceProviders)
    {
        foreach ($identityProviders as $identityProvider) {
            if (!$identityProvider instanceof IdentityProvider) {
                throw new InvalidArgumentException("Gave a non-idp to InMemoryMetadataRepository idps");
            }
            $this->identityProviders[$identityProvider->entityId] = $identityProvider;
        }

        foreach ($serviceProviders as $serviceProvider) {
            if (!$serviceProvider instanceof ServiceProvider) {
                throw new InvalidArgumentException("Gave a non-sp to InMemoryMetadataRepository sps");
            }
            $this->serviceProviders[$serviceProvider->entityId] = $serviceProvider;
        }
    }

    /**
     * @param array $repositoryConfig
     * @param \EngineBlock_Application_DiContainer $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container)
    {
        return new static(array(), array());
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return $this
     */
    public function registerServiceProvider(ServiceProvider $serviceProvider)
    {
        $this->serviceProviders[$serviceProvider->entityId] = $serviceProvider;
        return $this;
    }

    /**
     * @param IdentityProvider $identityProviderEntity
     * @return $this
     */
    public function registerIdentityProvider(IdentityProvider $identityProviderEntity)
    {
        $this->identityProviders[$identityProviderEntity->entityId] = $identityProviderEntity;
        return $this;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        if (!isset($this->identityProviders[$entityId])) {
            return null;
        }

        return $this->identityProviders[$entityId];
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        if (!isset($this->serviceProviders[$entityId])) {
            return null;
        }

        return $this->serviceProviders[$entityId];
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        return $this->identityProviders;
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        /** @var AbstractRole[] $entities */
        $entities = $this->identityProviders + $this->serviceProviders;

        $publishableEntities = array();
        foreach ($entities as $entity) {
            if (!$entity->publishInEdugain) {
                continue;
            }

            $publishableEntities[] = $entity;
        }
        return $publishableEntities;
    }
}
