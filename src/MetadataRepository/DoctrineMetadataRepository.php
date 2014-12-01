<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\DisableDisallowedEntitiesInWayfFilter;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\RemoveDisallowedIdentityProvidersFilter;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\RemoveEntityByEntityId;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\RemoveOtherWorkflowStatesFilter;
use RuntimeException;

class DoctrineMetadataRepository extends AbstractMetadataRepository
{
    /**
     * @var EntityRepository
     */
    private $spRepository;

    /**
     * @var EntityRepository
     */
    private $idpRepository;

    /**
     * @param array $repositoryConfig
     * @param ContainerInterface $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, ContainerInterface $container)
    {
        /** @var EntityManager $em */
        $em = $container->getEntityManager();
        $idpRepository = $em->getRepository('OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider');
        $spRepository  = $em->getRepository('OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider');

        return new self($spRepository, $idpRepository);
    }

    protected function __construct(EntityRepository $spRepository, EntityRepository $idpRepository)
    {
        parent::__construct();

        $this->spRepository = $spRepository;
        $this->idpRepository = $idpRepository;
    }

    /**
     *
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds()
    {
        return $this->idpRepository->createQueryBuilder('idp')->select('entityId')->getQuery()->execute();
    }

    /**
     * Find all SchacHomeOrganizations that are reserved by Identity Providers.
     *
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations()
    {
        return $this->idpRepository
            ->createQueryBuilder('idp')
            ->select('schacHomeOrganization')
            ->distinct()
            ->orderBy('schacHomeOrganization')
            ->getQuery()
            ->execute();
    }

    /**
     *
     * NOTE: Highly inefficient default (in-memory) method that you probably want to override.
     *
     * @param array $IdentityProviderIds
     * @return array|IdentityProvider[]
     * @throws EntityNotFoundException
     */
    public function fetchIdentityProvidersByEntityId(array $IdentityProviderIds)
    {
        $identityProviders = $this->findIdentityProviders();

        $filteredIdentityProviders = array();
        foreach ($IdentityProviderIds as $IdentityProviderId) {
            if (!isset($identityProviders[$IdentityProviderId])) {
                throw new EntityNotFoundException(
                    "Did not find an Identity Provider with entityId '$IdentityProviderId'"
                );
            }

            $filteredIdentityProviders[$IdentityProviderId] = $identityProviders[$IdentityProviderId];
        }
        return $filteredIdentityProviders;
    }

    /**
     * @param string $entityId
     * @return IdentityProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        /** @var IdentityProvider|null $identityProvider */
        $identityProvider = $this->idpRepository->findOneBy(array('entityId' => $entityId));

        return $this->filterCollection->filterEntity(
            $identityProvider
        );
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        /** @var ServiceProvider|null $serviceProvider */
        $serviceProvider = $this->spRepository->findOneBy(array('entityId' => $entityId));

        if (!$serviceProvider) {
            return null;
        }

        return $this->filterCollection->filterEntity(
            $serviceProvider
        );
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        return $this->idpRepository->findAll();
    }

    /**
     *
     * NOTE: Inefficient default (in-memory) method that you probably want to override.
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

        return null;
    }

    /**
     *
     * NOTE: Default (empty) method that you probably want to override.
     *
     * @param AbstractRole $entity
     * @return string
     */
    public function fetchEntityManipulation(AbstractRole $entity)
    {
        return '';
    }

    /**
     *
     * NOTE: Default (empty) method that you probably want to override.
     *
     * @param ServiceProvider $serviceProvider
     * @return AttributeReleasePolicy
     */
    public function fetchServiceProviderArp(ServiceProvider $serviceProvider)
    {
        return null;
    }

    /**
     *
     * NOTE: Default (unfiltered) method that you probably want to override.
     *
     * @param ServiceProvider $serviceProvider
     * @return \string[]
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        return $this->findAllIdentityProviderEntityIds();
    }

    /**
     * @return AbstractRole[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        $result = array();
        $result = array_merge($result, $this->idpRepository->findBy(array('publishableInEdugain' => true)));
        $result = array_merge($result, $this->spRepository->findBy(array('publishableInEdugain' => true)));
        return $result;
    }
}
