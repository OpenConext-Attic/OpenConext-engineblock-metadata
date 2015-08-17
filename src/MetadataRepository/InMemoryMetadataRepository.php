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
     * @var array<string,AbstractRole[]>
     */
    private $entities = array();

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
            $this->registerIdentityProvider($identityProvider);
        }

        foreach ($serviceProviders as $serviceProvider) {
            $this->registerServiceProvider($serviceProvider);
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
        $this->serviceProviders[] = $serviceProvider;

        return $this->registerEntityRole($serviceProvider);
    }

    /**
     * @param IdentityProvider $identityProvider
     * @return $this
     */
    public function registerIdentityProvider(IdentityProvider $identityProvider)
    {
        $this->identityProviders[] = $identityProvider;

        return $this->registerEntityRole($identityProvider);
    }

    /**
     * @param AbstractRole $serviceProvider
     */
    private function registerEntityRole(AbstractRole $role)
    {
        if (!isset($this->entities[$role->entityId])) {
            $this->entities[$role->entityId] = array();
        }

        $this->entities[$role->entityId][] = $role;

        return $this;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        $roles = $this->findIdentityProviderRolesByEntityId($entityId);
        if (empty($roles)) {
            return null;
        }

        $role = $this->findFilteredRole($roles);
        if (!$role) {
            return null;
        }

        $role->accept($this->compositeVisitor);

        return $role;
    }

    /**
     * @param $entityId
     * @return array
     */
    private function findIdentityProviderRolesByEntityId($entityId)
    {
        if (empty($this->entities[$entityId])) {
            return null;
        }

        $idpRoles = array();
        foreach ($this->entities[$entityId] as $role) {
            if (!$role instanceof IdentityProvider) {
                continue;
            }

            $idpRoles[] = $role;
        }
        return $idpRoles;
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        $roles = $this->findServiceProviderRolesByEntityId($entityId);
        if (empty($roles)) {
            return null;
        }

        $role = $this->findFilteredRole($roles);
        if (!$role) {
            return null;
        }

        $role->accept($this->compositeVisitor);

        return $role;
    }

    /**
     * @param $entityId
     * @return array
     */
    private function findServiceProviderRolesByEntityId($entityId)
    {
        if (empty($this->entities[$entityId])) {
            return null;
        }

        $spRoles = array();
        foreach ($this->entities[$entityId] as $role) {
            if (!$role instanceof ServiceProvider) {
                continue;
            }

            $spRoles[] = $role;
        }
        return $spRoles;
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

        $indexedIdentityProviders = array();
        foreach ($identityProviders as $identityProvider) {
            $indexedIdentityProviders[$identityProvider->entityId] = $identityProvider;
        }
        return $indexedIdentityProviders;
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain(MetadataRepositoryInterface $repository = NULL)
    {
        /** @var AbstractRole[] $roles */
        $roles = array_merge($this->identityProviders, $this->serviceProviders);

        $publishableRoles = array();
        foreach ($roles as $role) {
            if (!$role->publishInEdugain) {
                continue;
            }

            $publishableRoles[] = $role;
        }

        $roles = $this->compositeFilter->filterRoles(
            $publishableRoles
        );

        foreach ($roles as $role) {
            $role->accept($this->compositeVisitor);
        }
        return $roles;
    }

    /**
     * @param AbstractRole[] $roles
     * @return AbstractRole|null
     */
    private function findFilteredRole(array $roles)
    {
        $filteredRoles = array();
        foreach ($roles as $role) {
            $role = $this->compositeFilter->filterRole($role);

            if (!$role) {
                continue;
            }

            $filteredRoles[] = $role;
        }

        if (empty($filteredRoles)) {
            return null;
        }

        if (count($filteredRoles) > 2) {
            throw new \RuntimeException('Multiple roles matching after filtering!');
        }

        return array_shift($filteredRoles);
    }
}
