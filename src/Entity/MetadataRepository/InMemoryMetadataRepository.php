<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository;

use InvalidArgumentException;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class InMemoryMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var ServiceProviderEntity[]
     */
    private $serviceProviders = array();

    /**
     * @var IdentityProviderEntity[]
     */
    private $identityProviders = array();

    /**
     * @param IdentityProviderEntity[] $identityProviders
     * @param ServiceProviderEntity[] $serviceProviders
     * @throws InvalidArgumentException
     */
    public function __construct(array $identityProviders, array $serviceProviders)
    {
        foreach ($identityProviders as $identityProvider) {
            if (!$identityProvider instanceof IdentityProviderEntity) {
                throw new InvalidArgumentException("Gave a non-idp to InMemoryMetadataRepository idps");
            }
            $this->identityProviders[$identityProvider->entityId] = $identityProvider;
        }

        foreach ($serviceProviders as $serviceProvider) {
            if (!$serviceProvider instanceof ServiceProviderEntity) {
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
     * @param ServiceProviderEntity $serviceProvider
     * @return $this
     */
    public function registerServiceProvider(ServiceProviderEntity $serviceProvider)
    {
        $this->serviceProviders[$serviceProvider->entityId] = $serviceProvider;
        return $this;
    }

    /**
     * @param IdentityProviderEntity $identityProviderEntity
     * @return $this
     */
    public function registerIdentityProvider(IdentityProviderEntity $identityProviderEntity)
    {
        $this->identityProviders[$identityProviderEntity->entityId] = $identityProviderEntity;
        return $this;
    }

    /**
     * @param string $entityId
     * @return ServiceProviderEntity|null
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
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        if (!isset($this->serviceProviders[$entityId])) {
            return null;
        }

        return $this->serviceProviders[$entityId];
    }

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders()
    {
        return $this->identityProviders;
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        /** @var AbstractConfigurationEntity[] $entities */
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