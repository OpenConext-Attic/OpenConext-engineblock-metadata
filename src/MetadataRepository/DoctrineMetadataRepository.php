<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Class DoctrineMetadataRepository
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
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

    /**
     * @param EntityRepository $spRepository
     * @param EntityRepository $idpRepository
     */
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
        $queryBuilder = $this->idpRepository->createQueryBuilder('idp')->select('entityId');

        $this->filterCollection->toQueryBuilder($queryBuilder);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * Find all SchacHomeOrganizations that are reserved by Identity Providers.
     *
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations()
    {
        $queryBuilder = $this->idpRepository
            ->createQueryBuilder('idp')
            ->select('schacHomeOrganization')
            ->distinct()
            ->orderBy('schacHomeOrganization');

        $this->filterCollection->toQueryBuilder($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->execute();
    }

    /**
     *
     * NOTE: Highly inefficient default (in-memory) method that you probably want to override.
     *
     * @param array $identityProviderIds
     * @return array|IdentityProvider[]
     * @throws EntityNotFoundException
     */
    public function fetchIdentityProvidersByEntityId(array $identityProviderIds)
    {
        $identityProviders = $this->findIdentityProviders();

        $filteredIdentityProviders = array();
        foreach ($identityProviderIds as $identityProviderId) {
            if (!isset($identityProviders[$identityProviderId])) {
                throw new EntityNotFoundException(
                    "Did not find an Identity Provider with entityId '$identityProviderId'"
                );
            }

            $filteredIdentityProviders[$identityProviderId] = $identityProviders[$identityProviderId];
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
        $identityProvider = $this->idpRepository->matching(
            $this->filterCollection->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

        return $this->applyVisitors($identityProvider);
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        /** @var ServiceProvider|null $serviceProvider */
        $serviceProvider = $this->spRepository->matching(
            $this->filterCollection->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

        if (!$serviceProvider) {
            return null;
        }

        return $this->applyVisitors(
            $serviceProvider
        );
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        return $this->idpRepository->matching(
            $this->filterCollection->toCriteria()
        )->toArray();
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
