<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use InvalidArgumentException;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
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
        parent::__construct();

        foreach ($identityProviders as $identityProvider) {
            if (!$identityProvider instanceof IdentityProvider) {
                throw new InvalidArgumentException('Gave a non-idp to InMemoryMetadataRepository idps');
            }
            $this->identityProviders[$identityProvider->entityId] = $identityProvider;
        }

        foreach ($serviceProviders as $serviceProvider) {
            if (!$serviceProvider instanceof ServiceProvider) {
                throw new InvalidArgumentException('Gave a non-sp to InMemoryMetadataRepository sps');
            }
            $this->serviceProviders[$serviceProvider->entityId] = $serviceProvider;
        }
    }

    /**
     * @param array $repositoryConfig
     * @param ContainerInterface $container
     * @return static
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container)
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

        $identityProvider = $this->compositeFilter->filterRole(
            $this->identityProviders[$entityId]
        );
        if (!$identityProvider) {
            return null;
        }

        $identityProvider->accept($this->compositeVisitor);
        return $identityProvider;
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

        $serviceProvider = $this->serviceProviders[$entityId];
        $serviceProvider = $this->compositeFilter->filterRole($serviceProvider);
        if (!$serviceProvider) {
            return null;
        }

        $serviceProvider->accept($this->compositeVisitor);
        return $serviceProvider;
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        $identityProviders = $this->compositeFilter->filterRoles(
            $this->identityProviders
        );

        foreach ($identityProviders as $identityProvider) {
            $identityProvider->accept($this->compositeVisitor);
        }
        return $identityProviders;
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain(MetadataRepositoryInterface $repository = NULL)
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

        $roles = $this->compositeFilter->filterRoles(
            $publishableEntities
        );

        foreach ($roles as $role) {
            $role->accept($this->compositeVisitor);
        }
        return $roles;
    }
}
