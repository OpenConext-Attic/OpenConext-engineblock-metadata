<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;;

use AD7six\Dsn\Dsn;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Janus\ServiceRegistry\Entity\Connection;
use Janus\ServiceRegistry\Entity\ConnectionRepository;
use OpenConext\Component\EngineBlockMetadata\Entity\Repository\Filter\FilterInterface;
use OpenConext\Component\EngineBlockMetadata\Translator\JanusTranslator;

class JanusMetadataRepository implements MetadataRepositoryInterface
{
    private $entityManager;
    private $translator;

    /**
     * @param array $repositoryConfig
     * @param \EngineBlock_Application_DiContainer $container
     * @return mixed|JanusMetadataRepository
     * @throws \RuntimeException
     */
    public static function createFromConfig(array $repositoryConfig, \EngineBlock_Application_DiContainer $container)
    {
        if (!isset($repositoryConfig['dsn'])) {
            throw new \RuntimeException('No dsn configured for JanusMetadataRepository');
        }

        /** @var Dsn $dsn */
        $dsn = Dsn::parse($repositoryConfig['dsn']);
        $dsnArray = $dsn->toArray();

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), isset($repositoryConfig['dev']) ? true : false);
        $entityManager = EntityManager::create($dsnArray, $config);

        return new self($entityManager, new JanusTranslator());
    }

    public function __construct(
        EntityManager $entityManager,
        JanusTranslator $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function fetchEntityByEntityId($entityId)
    {
        /** @var ConnectionRepository $connectionRepository */
        $connectionRepository = $this->entityManager->getRepository('Janus\Component\ReadonlyEntities\Entities\Connection');
        $connections = $connectionRepository->findBy(array('name' => $entityId, 'active' => true));

        if (empty($connections)) {
            throw new \RuntimeException("No entities found for '$entityId'");
        }
        if (count($connections) > 1) {
            throw new \RuntimeException("Multiple connections found for '$entityId'");
        }

        return $this->translator->translate($connections[0]);
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function fetchAllEntities()
    {
        /** @var ConnectionRepository $connectionRepository */
        $connectionRepository = $this->entityManager->getRepository(
            'Janus\Component\ReadonlyEntities\Entities\Connection'
        );
        /** @var Connection[] $connections */
        $connections = $connectionRepository->findBy(array('active' => true));

        /** @todo come caching here? */
        $entities = array();
        foreach ($connections as $connection) {
            $entities[$connection->getName()] = $this->translator->translate($connection);
        }
        return $entities;
    }

    /**
     * @param string $idpEntityId
     * @return IdentityProviderEntity
     */
    public function fetchIdentityProviderByEntityId($idpEntityId)
    {
        // TODO: Implement fetchIdentityProviderByEntityId() method.
    }

    /**
     * @param string $spEntityId
     * @return ServiceProviderEntity|null
     */
    public function findIdentityProviderByEntityId($spEntityId)
    {
        // TODO: Implement findIdentityProviderByEntityId() method.
    }

    /**
     * @param string $spEntityId
     * @return ServiceProviderEntity
     */
    public function fetchServiceProviderByEntityId($spEntityId)
    {
        // TODO: Implement fetchServiceProviderByEntityId() method.
    }

    /**
     * @param $spEntityId
     * @return ServiceProviderEntity|null
     */
    public function findServiceProviderByEntityId($spEntityId)
    {
        // TODO: Implement findServiceProviderByEntityId() method.
    }

    /**
     * @return ServiceProviderEntity[]
     */
    public function findServiceProviders()
    {
        // TODO: Implement findServiceProviders() method.
    }

    /**
     * @return IdentityProviderEntity[]
     */
    public function findIdentityProviders()
    {
        // TODO: Implement findIdentityProviders() method.
    }

    /**
     * @return string[]
     */
    public function findAllIdentityProviderEntityIds()
    {
        // TODO: Implement findAllIdentityProviderEntityIds() method.
    }

    /**
     * @return string[]
     */
    public function findReservedSchacHomeOrganizations()
    {
        // TODO: Implement findReservedSchacHomeOrganizations() method.
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function findEntitiesPublishableInEdugain()
    {
        // TODO: Implement findEntitiesPublishableInEdugain() method.
    }

    /**
     * @param ServiceProviderEntity $serviceProvider
     * @param IdentityProviderEntity $identityProvider
     * @return bool
     */
    public function isConnectionAllowed(ServiceProviderEntity $serviceProvider, IdentityProviderEntity $identityProvider)
    {
        // TODO: Implement isConnectionAllowed() method.
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function filter(FilterInterface $filter)
    {
        // TODO: Implement filter() method.
    }

    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity|null
     */
    public function findEntityByEntityId($entityId)
    {
        // TODO: Implement findEntityByEntityId() method.
    }


}