<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;
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
        /** @var IdentityProvider|null $identityProvider */
        $identityProvider = $this->idpRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->in('entityId', $identityProviderIds))
        );

        $identityProvider->accept($this->compositeVisitor);
        return $identityProvider;
    }

    /**
     * @param string $entityId
     * @return IdentityProvider|null
     */
    public function findIdentityProviderByEntityId($entityId)
    {
        /** @var IdentityProvider|null $identityProvider */
        $identityProvider = $this->idpRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

        $identityProvider->accept($this->compositeVisitor);
        return $identityProvider;
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        /** @var ServiceProvider|null $serviceProvider */
        $serviceProvider = $this->spRepository->matching(
            $this->compositeFilter->toCriteria()
                ->andWhere(Criteria::expr()->eq('entityId', $entityId))
        );

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
