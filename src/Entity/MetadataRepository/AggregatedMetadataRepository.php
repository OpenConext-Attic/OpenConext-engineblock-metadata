<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository;

use Janus\ServiceRegistry\Entity\Connection\Revision\Metadata;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter\DisableDisallowedEntitiesInWayfFilter;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter\RemoveDisallowedIdentityProvidersFilter;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class AggregatedMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var MetadataRepositoryInterface[]
     */
    private $orderedRepositories = array();

    /**
     * @param array $repositoryConfig
     * @param \EngineBlock_Application_DiContainer $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container)
    {
        $factory = new RepositoryFactory();

        $orderedRepositories = array();
        foreach ($repositoryConfig as $nestedRepositoryConfig) {
            $orderedRepositories[] = $factory->createFromConfig($nestedRepositoryConfig, $container);
        }

        return new static($orderedRepositories);
    }

    /**
     * @param MetadataRepositoryInterface[] $orderedRepositories
     */
    public function __construct(array $orderedRepositories)
    {
        $this->orderedRepositories = $orderedRepositories;
    }

    /**
     * @param MetadataRepositoryInterface $repository
     * @return $this
     */
    public function appendRepository(MetadataRepositoryInterface $repository)
    {
        $this->orderedRepositories[] = $repository;
        return $this;
    }

    /**
     *
     * @param string $entityId
     * @return AbstractConfigurationEntity
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findEntityByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }

        throw new EntityNotFoundException("Unable to find '$entityId' in any configured repository");
    }

    /**
     * @param string $entityId
     * @return ServiceProviderEntity
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findServiceProviderByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }

        throw new EntityNotFoundException("Unable to find '$entityId' in any configured repository");
    }

    /**
     * @param string $entityId
     * @return IdentityProviderEntity
     * @throws EntityNotFoundException
     */
    public function fetchIdentityProviderByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findIdentityProviderByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }

        throw new EntityNotFoundException("Unable to find '$entityId' in any configured repository");
    }

    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity|null
     */
    public function findEntityByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findEntityByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }
        return null;
    }

    /**
     * @param string $entityId
     * @return ServiceProviderEntity|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findIdentityProviderByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }
        return null;
    }

    /**
     * @param $entityId
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        foreach ($this->orderedRepositories as $repository) {
            $entity = $repository->findServiceProviderByEntityId($entityId);

            if ($entity) {
                return $entity;
            }
        }
        return null;
    }

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders()
    {
        $identityProviders = array();
        foreach ($this->orderedRepositories as $repository) {
            $identityProviders = array_merge($identityProviders, $repository->findIdentityProviders());
        }
        return $identityProviders;
    }

    /**
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds()
    {
        $identityProviderEntityIds = array();
        foreach ($this->orderedRepositories as $repository) {
            $identityProviderEntityIds = array_merge($identityProviderEntityIds, $repository->findAllIdentityProviderEntityIds());
        }
        return $identityProviderEntityIds;
    }

    /**
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations()
    {
        $schacHomeOrganizations = array();
        foreach ($this->orderedRepositories as $repository) {
            $schacHomeOrganizations = array_merge($schacHomeOrganizations, $repository->findReservedSchacHomeOrganizations());
        }
        return $schacHomeOrganizations;
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        $entities = array();
        foreach ($this->orderedRepositories as $repository) {
            $entities = array_merge($entities, $repository->findEntitiesPublishableInEdugain());
        }
        return $entities;
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function filter(FilterInterface $filter)
    {
        foreach ($this->orderedRepositories as $repository) {
            $filter = clone $filter;
            if ($filter instanceof RemoveDisallowedIdentityProvidersFilter) {
                $filter->setRepository($repository);
            }
            if ($filter instanceof DisableDisallowedEntitiesInWayfFilter) {
                $filter->setRepository($repository);
            }
            $repository->filter($filter);
        }
        return $this;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @return string
     * @throws \RuntimeException
     */
    public function fetchEntityManipulation(AbstractConfigurationEntity $entity)
    {
        foreach ($this->orderedRepositories as $repository) {
            if (!$repository->findEntityByEntityId($entity->entityId)) {
                continue;
            }

            return $repository->fetchEntityManipulation($entity);
        }

        throw new \RuntimeException(
            __METHOD__ . ' was unable to find a repository for entity: ' . $entity->entityId
        );
    }

    /**
     * @param ServiceProviderEntity $serviceProvider
     * @return AttributeReleasePolicy
     * @throws \RuntimeException
     */
    public function fetchServiceProviderArp(ServiceProviderEntity $serviceProvider)
    {
        foreach ($this->orderedRepositories as $repository) {
            if (!$repository->findServiceProviderByEntityId($serviceProvider->entityId)) {
                continue;
            }

            return $repository->fetchServiceProviderArp($serviceProvider);
        }

        throw new \RuntimeException(
            __METHOD__ . ' was unable to find a repository for SP: ' . $serviceProvider->entityId
        );
    }

    /**
     * @param ServiceProviderEntity $serviceProvider
     * @param IdentityProviderEntity $identityProvider
     * @return bool
     * @throws \RuntimeException
     */
    public function isConnectionAllowed(ServiceProviderEntity $serviceProvider, IdentityProviderEntity $identityProvider)
    {
        foreach ($this->orderedRepositories as $repository) {
            $hasServiceProvider = $repository->findServiceProviderByEntityId($serviceProvider->entityId);

            if (!$hasServiceProvider) {
                continue;
            }

            $hasIdentityProvider = $repository->findIdentityProviderByEntityId($identityProvider->entityId);

            if (!$hasIdentityProvider) {
                continue;
            }

            return $repository->isConnectionAllowed($serviceProvider, $identityProvider);
        }

        throw new \RuntimeException(
            __METHOD__ . ' was unable to find a repository for SP: ' . $serviceProvider->entityId .
                ' and IdP: ' . $identityProvider->entityId
        );
    }
}