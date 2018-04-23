<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface;
use Psr\Log\LoggerInterface;

/**
 * Caching wrapper around DoctrineMetadataRepository.
 *
 * This repository acts as the regular DoctrineRepository, but caches the
 * result of each method invocation in-memory so queries are never executed
 * more than once per request.
 *
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 *
 * @SuppressWarnings(PMD.TooManyPublicMethods)
 */
class CachedDoctrineMetadataRepository implements MetadataRepositoryInterface
{
    /**
     * Query result cache.
     *
     * @var array
     */
    private $cache = array();

    /**
     * @var DoctrineMetadataRepository
     */
    private $repository = array();

    /**
     * @param array $repositoryConfig
     * @param ContainerInterface $container
     * @return CachedDoctrineMetadataRepository
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container)
    {
        return new self(
            DoctrineMetadataRepository::createFromConfig($repositoryConfig, $container)
        );
    }

    /**
     * @param DoctrineMetadataRepository $repository
     */
    public function __construct(DoctrineMetadataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Read results from cache or proxy to wrapped doctrine repository.
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function invoke($name, array $args)
    {
        $signature = $name . ':' . serialize($args);

        if (!isset($this->cache[$signature])) {
            $this->cache[$signature] = call_user_func_array(array($this->repository, $name), $args);
        }

        return $this->cache[$signature];
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function appendFilter(FilterInterface $filter)
    {
        $this->repository->appendFilter($filter);

        $this->clearResultsCache();

        return $this;
    }

    /**
     * @param VisitorInterface $visitor
     * @return $this
     */
    public function appendVisitor(VisitorInterface $visitor)
    {
        $this->repository->appendVisitor($visitor);

        $this->clearResultsCache();

        return $this;
    }

    /**
     * Reset the results cache.
     *
     * The cache is only valid for a specific combination of filters and
     * visitors. If a filter or visitor is appended, the previously cached
     * results are discarded by calling this method. In practice,
     * visitor/filter setup is only done in the beginning of the request so
     * resetting the cache has little impact on the total number of queries.
     */
    private function clearResultsCache()
    {
        $this->cache = array();
    }

    /**
     * @param string $entityId
     * @return AbstractRole
     * @throws EntityNotFoundException
     */
    public function fetchEntityByEntityId($entityId)
    {
        $entity = $this->findEntityByEntityId($entityId);

        if (!$entity) {
            throw new EntityNotFoundException("Entity '$entityId' not found in database");
        }

        return $entity;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider
     * @throws EntityNotFoundException
     */
    public function fetchServiceProviderByEntityId($entityId)
    {
        $serviceProvider = $this->findServiceProviderByEntityId($entityId);

        if (!$serviceProvider) {
            throw new EntityNotFoundException("Service Provider '$entityId' not found in database");
        }

        return $serviceProvider;
    }

    /**
     * @param string $entityId
     * @return IdentityProvider
     */
    public function fetchIdentityProviderByEntityId($entityId)
    {
        $identityProvider = $this->findIdentityProviderByEntityId($entityId);

        if (!$identityProvider) {
            throw new EntityNotFoundException("Identity Provider '$entityId' not found in database");
        }

        return $identityProvider;
    }

    /**
     * @deprecated Don't use this method: entity ID is NOT unique, in theory,
     *             service- and identity providers can share the same entity ID.
     *
     * @param string $entityId
     * @return AbstractRole|null
     */
    public function findEntityByEntityId($entityId)
    {
        $serviceProvider = $this->findServiceProviderByEntityId($entityId);
        if ($serviceProvider) {
            return $serviceProvider;
        }

        $identityProvider = $this->findIdentityProviderByEntityId($entityId);
        if ($identityProvider) {
            return $identityProvider;
        }
    }

    /**
     * @param string $entityId
     * @return IdentityProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @param $entityId
     * @param LoggerInterface|null $logger
     * @return null|ServiceProvider
     */
    public function findServiceProviderByEntityId($entityId, LoggerInterface $logger = null)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @param array $identityProviderEntityIds
     * @return IdentityProvider[]
     */
    public function findIdentityProvidersByEntityId(array $identityProviderEntityIds)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds()
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations()
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain(MetadataRepositoryInterface $repository = null)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @param AbstractRole $entity
     * @return string
     */
    public function fetchEntityManipulation(AbstractRole $entity)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return AttributeReleasePolicy
     */
    public function fetchServiceProviderArp(ServiceProvider $serviceProvider)
    {
        return $this->invoke(__FUNCTION__, func_get_args());
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return array
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        // This is a simple getter. Cache this.
        return $this->repository->findAllowedIdpEntityIdsForSp($serviceProvider);
    }
}
