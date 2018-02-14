<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface;
use Psr\Log\LoggerInterface;

/**
 * Class CompositeMetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.TooManyPublicMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class CompositeMetadataRepository extends AbstractMetadataRepository
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
        parent::__construct();

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
     * {@inheritdoc}
     */
    public function appendVisitor(VisitorInterface $visitor)
    {
        foreach ($this->orderedRepositories as $repository) {
            $repository->appendVisitor($visitor);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function appendFilter(FilterInterface $filter)
    {
        foreach ($this->orderedRepositories as $repository) {
            $repository->appendFilter(clone $filter);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findServiceProviderByEntityId($entityId, LoggerInterface $logger = null)
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
     * {@inheritdoc}
     */
    public function findIdentityProviders()
    {
        $identityProviders = array();
        foreach ($this->orderedRepositories as $repository) {
            $repositoryIdentityProviders = $repository->findIdentityProviders();
            foreach ($repositoryIdentityProviders as $identityProvider) {
                // Earlier repositories have precedence, so if later repositories give the same entityId,
                // then we ignore that.
                if (isset($identityProviders[$identityProvider->entityId])) {
                    continue;
                }

                $identityProviders[$identityProvider->entityId] = $identityProvider;
            }
        }
        return $identityProviders;
    }

    /**
     * {@inheritdoc}
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
        return array_values(array_unique($identityProviderEntityIds));
    }

    /**
     * {@inheritdoc}
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
        return array_values(array_unique($schacHomeOrganizations));
    }

    /**
     * {@inheritdoc}
     */
    public function findEntitiesPublishableInEdugain(MetadataRepositoryInterface $repository = null)
    {
        $entityIndex = array();
        $entities = array();
        foreach ($this->orderedRepositories as $repository) {
            $repositoryEntities = $repository->findEntitiesPublishableInEdugain($this);
            foreach ($repositoryEntities as $repositoryEntity) {
                // When is an entity the same as another one? For now when it's the same role type (SP / IDP)
                // and has the same entityId. Though the SAML2 spec allows for much more than that,
                // we currently don't support anything more.
                // Note that we avoid an O(n3) lookup here by maintaining an index.
                $index = get_class($repositoryEntity) . ':' . $repositoryEntity->entityId;
                if (in_array($index, $entityIndex)) {
                    continue;
                }

                $entityIndex[] = $index;
                $entities[] = $repositoryEntity;
            }
        }
        return $entities;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        $allowed = array();

        foreach ($this->orderedRepositories as $repository) {
            if ($repository->findServiceProviderByEntityId($serviceProvider->entityId)) {
                $allowed = $repository->findAllowedIdpEntityIdsForSp($serviceProvider);

                break;
            }
        }

        return array_values(array_unique($allowed));
    }
}
