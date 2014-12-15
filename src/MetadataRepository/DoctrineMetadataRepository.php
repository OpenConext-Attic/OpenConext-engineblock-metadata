<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use RuntimeException;

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
     * @return self
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
    public function __construct(EntityRepository $spRepository, EntityRepository $idpRepository)
    {
        parent::__construct();

        $this->spRepository  = $spRepository;
        $this->idpRepository = $idpRepository;
    }

    /**
     *
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds()
    {
        $queryBuilder = $this->idpRepository
            ->createQueryBuilder('role')
            ->select('role.entityId');

        $this->compositeFilter->toQueryBuilder($queryBuilder);

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
            ->createQueryBuilder('role')
            ->select('role.schacHomeOrganization')
            ->distinct()
            ->orderBy('role.schacHomeOrganization');

        $this->compositeFilter->toQueryBuilder($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $identityProviderIds
     * @return array|IdentityProvider[]
     * @throws EntityNotFoundException
     */
    public function findIdentityProvidersByEntityId(array $identityProviderIds)
    {
        $identityProviders = $this->idpRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->in('entityId', $identityProviderIds))
        )->toArray();

        foreach ($identityProviders as $identityProvider) {
            if (!$identityProvider instanceof IdentityProvider) {
                throw new RuntimeException('Non-IdentityProvider found');
            }

            $identityProvider->accept($this->compositeVisitor);
        }

        return $identityProviders;
    }

    /**
     * @param string $entityId
     * @return IdentityProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        $identityProviderCollection = $this->idpRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

        if ($identityProviderCollection->count() === 0) {
            return null;
        }

        if ($identityProviderCollection->count() > 1) {
            throw new RuntimeException('Multiple Identity Providers found for entityId: ' . $entityId);
        }

        $identityProvider = $identityProviderCollection->first();
        if (!$identityProvider instanceof IdentityProvider) {
            throw new RuntimeException('Entity found for entityId: ' . $entityId . ' is not an Identity Provider!');
        }

        $identityProvider->accept($this->compositeVisitor);

        return $identityProvider;
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        $serviceProviderCollection = $this->spRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

        if ($serviceProviderCollection->count() === 0) {
            return null;
        }

        if ($serviceProviderCollection->count() > 1) {
            throw new RuntimeException('Multiple Identity Providers found for entityId: ' . $entityId);
        }

        $serviceProvider = $serviceProviderCollection->first();
        if (!$serviceProvider instanceof ServiceProvider) {
            throw new RuntimeException('Entity found for entityId: ' . $entityId . ' is not an ServiceProvider!');
        }

        if (!$serviceProvider) {
            return null;
        }

        $serviceProvider->accept($this->compositeVisitor);

        return $serviceProvider;
    }

    /**
     * @return IdentityProvider[]
     */
    public function findIdentityProviders()
    {
        return $this->idpRepository->matching(
            $this->compositeFilter->toCriteria()
        )->toArray();
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @return \string[]
     */
    public function findAllowedIdpEntityIdsForSp(ServiceProvider $serviceProvider)
    {
        return $serviceProvider->allowedIdpEntityIds;
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
