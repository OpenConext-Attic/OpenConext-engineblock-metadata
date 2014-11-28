<?php

namespace OpenConext\EngineBlock\MetadataRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OpenConext\EngineBlock\AttributeReleasePolicy;
use OpenConext\EngineBlock\Entity\AbstractRole;
use OpenConext\EngineBlock\Entity\IdentityProvider;
use OpenConext\EngineBlock\Entity\ServiceProvider;

class DatabaseMetadataRepository extends AbstractMetadataRepository
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
     * @param \EngineBlock_Application_DiContainer $container
     * @return mixed
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container)
    {
        /** @var EntityManager $em */
        $em = $container->getEntityManager();
        $idpRepository = $em->getRepository('OpenConext\EngineBlock\Entity\IdentityProvider');
        $spRepository  = $em->getRepository('OpenConext\EngineBlock\Entity\ServiceProvider');

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
     *
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
        return $this->idpRepository->findBy(array('entityId' => $entityId));
    }

    /**
     * @param $entityId
     * @return ServiceProvider|null
     */
    public function findServiceProviderByEntityId($entityId)
    {
        return $this->filterCollection->filterEntity(
            $this->spRepository->findBy(array('entityId' => $entityId))
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
