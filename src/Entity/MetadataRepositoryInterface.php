<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Entity\Repository\Filter\FilterInterface;

interface MetadataRepositoryInterface
{
    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity
     */
    public function fetchEntityByEntityId($entityId);

    /**
     * @param string $idpEntityId
     * @return IdentityProviderEntity
     */
    public function fetchIdentityProviderByEntityId($idpEntityId);

    /**
     * @param string $spEntityId
     * @return ServiceProviderEntity|null
     */
    public function findIdentityProviderByEntityId($spEntityId);

    /**
     * @param string $spEntityId
     * @return ServiceProviderEntity
     */
    public function fetchServiceProviderByEntityId($spEntityId);

    /**
     * @param $spEntityId
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($spEntityId);

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function fetchAllEntities();

    /**
     * @return ServiceProviderEntity[]
     */
    public function findServiceProviders();

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders();

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
     * @param ServiceProviderEntity $serviceProvider
     * @param IdentityProviderEntity $identityProvider
     * @return bool
     */
    public function isConnectionAllowed(ServiceProviderEntity $serviceProvider, IdentityProviderEntity $identityProvider);

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function filter(FilterInterface $filter);

    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity|null
     */
    public function findEntityByEntityId($entityId);
}