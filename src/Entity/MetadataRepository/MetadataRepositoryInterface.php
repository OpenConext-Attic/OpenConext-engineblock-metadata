<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

/**
 * Interface MetadataRepositoryInterface
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository
 */
interface MetadataRepositoryInterface
{
    /**
     * @param array $repositoryConfig
     * @param \EngineBlock_Application_DiContainer $container
     * @return MetadataRepositoryInterface
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container);

    /**
     *
     * @param string $entityId
     * @return AbstractConfigurationEntity
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId);

    /**
     * @param string $entityId
     * @return ServiceProviderEntity
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId);

    /**
     * @param string $entityId
     * @return IdentityProviderEntity
     */
    public function fetchIdentityProviderByEntityId($entityId);

    /**
     * @deprecated depends on repository implementation.
     *
     * @param string $entityId
     * @return AbstractConfigurationEntity|null
     */
    public function findEntityByEntityId($entityId);

    /**
     * @param string $entityId
     * @return ServiceProviderEntity|null
     */
    public function findIdentityProviderByEntityId($entityId);

    /**
     * @param $entityId
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($entityId);

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders();

    /**
     * @param array $identityProviderEntityIds
     * @return IdentityProviderEntity[]
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
     * @return AbstractConfigurationEntity[]
     */
    public function findEntitiesPublishableInEdugain();

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function filter(FilterInterface $filter);

    /**
     * @param AbstractConfigurationEntity $entity
     * @return string
     */
    public function fetchEntityManipulation(AbstractConfigurationEntity $entity);

    /**
     * @param ServiceProviderEntity $serviceProvider
     * @return AttributeReleasePolicy
     */
    public function fetchServiceProviderArp(ServiceProviderEntity $serviceProvider);

    /**
     * @param ServiceProviderEntity $serviceProvider
     * @return array
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProviderEntity $serviceProvider);
}
