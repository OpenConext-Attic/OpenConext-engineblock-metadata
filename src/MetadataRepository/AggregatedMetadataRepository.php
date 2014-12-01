<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Class AggregatedMetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class AggregatedMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var MetadataRepositoryInterface[]
     */
    private $orderedRepositories = array();

    /**
     * @param array $repositoryConfig
     * @param ContainerInterface $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container)
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
     * @return AbstractRole
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
     * @return ServiceProvider
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
     * @return IdentityProvider
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
     * @return AbstractRole|null
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
     * @return ServiceProvider|null
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
     * @return ServiceProvider|null
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
     * @return IdentityProvider[]
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
            $identityProviderEntityIds = array_merge(
                $identityProviderEntityIds,
                $repository->findAllIdentityProviderEntityIds()
            );
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
            $schacHomeOrganizations = array_merge(
                $schacHomeOrganizations,
                $repository->findReservedSchacHomeOrganizations()
            );
        }
        return $schacHomeOrganizations;
    }

    /**
     * @return AbstractRole[]
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
            $repository->filter(clone $filter);
        }
        return $this;
    }

    /**
     * @param AbstractRole $entity
     * @return string
     * @throws \RuntimeException
     */
    public function fetchEntityManipulation(AbstractRole $entity)
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
     * @param ServiceProvider $serviceProvider
     * @return AttributeReleasePolicy
     * @throws \RuntimeException
     */
    public function fetchServiceProviderArp(ServiceProvider $serviceProvider)
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
     * @param ServiceProvider $serviceProvider
     * @return array|bool
     * @throws \RuntimeException
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        $allowed = array();
        foreach ($this->orderedRepositories as $repository) {
            $allowed = array_merge($allowed, $repository->findAllowedIdpEntityIdsForSp($serviceProvider));
        }

        return $allowed;
    }
}
