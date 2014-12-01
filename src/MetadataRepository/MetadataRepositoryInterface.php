<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Interface MetadataRepositoryInterface
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 */
interface MetadataRepositoryInterface
{
    /**
     * @param array $repositoryConfig
     * @param ContainerInterface $container
     * @return MetadataRepositoryInterface
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container);

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function registerFilter(FilterInterface $filter);

    /**
     *
     * @param string $entityId
     * @return AbstractRole
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId);

    /**
     * @param string $entityId
     * @return ServiceProvider
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId);

    /**
     * @param string $entityId
     * @return IdentityProvider
     */
    public function fetchIdentityProviderByEntityId($entityId);

    /**
     * @deprecated depends on repository implementation.
     *
     * @param string $entityId
     * @return AbstractRole|null
     */
    public function findEntityByEntityId($entityId);

    /**
     * @param string $entityId
     * @return ServiceProvider|null
     */
    public function findIdentityProviderByEntityId($entityId);

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId);

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders();

    /**
     * @param array $identityProviderEntityIds
     * @return IdentityProvider[]
     */
    public function fetchIdentityProvidersByEntityId(array $identityProviderEntityIds);

    /**
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds();

    /**
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations();

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain();

    /**
     * @param AbstractRole $entity
     * @return string
     */
    public function fetchEntityManipulation(AbstractRole $entity);

    /**
     * @param ServiceProvider $serviceProvider
     * @return AttributeReleasePolicy
     */
    public function fetchServiceProviderArp(ServiceProvider $serviceProvider);

    /**
     * @param ServiceProvider $serviceProvider
     * @return array
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider);
}
