<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\DisableDisallowedEntitiesInWayfVisitor;
use PHPUnit_Framework_TestCase;

/**
 * Class CachedDoctrineMetadataRepositoryTest
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 */
class CachedDoctrineMetadataRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testMethodsCallsAreProxied()
    {
        $doctrineRepository = Mockery::mock('OpenConext\Component\EngineBlockMetadata\MetadataRepository\DoctrineMetadataRepository');
        $doctrineRepository->shouldReceive('findIdentityProviderByEntityId');
        $doctrineRepository->shouldReceive('findServiceProviderByEntityId');
        $doctrineRepository->shouldReceive('findIdentityProviderByEntityId');
        $doctrineRepository->shouldReceive('findIdentityProviders');
        $doctrineRepository->shouldReceive('findIdentityProvidersByEntityId');
        $doctrineRepository->shouldReceive('findAllIdentityProviderEntityIds');
        $doctrineRepository->shouldReceive('findReservedSchacHomeOrganizations');
        $doctrineRepository->shouldReceive('findEntitiesPublishableInEdugain');
        $doctrineRepository->shouldReceive('fetchEntityManipulation');
        $doctrineRepository->shouldReceive('fetchServiceProviderArp');
        $doctrineRepository->shouldReceive('findAllowedIdpEntityIdsForSp');

        $sp = new ServiceProvider('test');

        $repository = new CachedDoctrineMetadataRepository($doctrineRepository);
        $repository->findIdentityProviderByEntityId('test');
        $repository->findServiceProviderByEntityId('test');
        $repository->findIdentityProviderByEntityId('test');
        $repository->findIdentityProviders();
        $repository->findIdentityProvidersByEntityId(['test']);
        $repository->findAllIdentityProviderEntityIds();
        $repository->findReservedSchacHomeOrganizations();
        $repository->findEntitiesPublishableInEdugain();
        $repository->fetchEntityManipulation($sp);
        $repository->fetchServiceProviderArp($sp);
        $repository->findAllowedIdpEntityIdsForSp($sp);
    }

    public function testFetchEntityThrowExceptions()
    {
        $doctrineRepository = Mockery::mock('OpenConext\Component\EngineBlockMetadata\MetadataRepository\DoctrineMetadataRepository');
        $doctrineRepository->shouldReceive('findEntityByEntityId');
        $doctrineRepository->shouldReceive('findServiceProviderByEntityId');
        $doctrineRepository->shouldReceive('findIdentityProviderByEntityId');

        $this->setExpectedException('OpenConext\\Component\\EngineBlockMetadata\\MetadataRepository\\EntityNotFoundException');

        $repository = new CachedDoctrineMetadataRepository($doctrineRepository);
        $repository->fetchEntityByEntityId('test');
    }

    public function testFetchIdentityProviderThrowExceptions()
    {
        $doctrineRepository = Mockery::mock('OpenConext\Component\EngineBlockMetadata\MetadataRepository\DoctrineMetadataRepository');
        $doctrineRepository->shouldReceive('findIdentityProviderByEntityId');

        $this->setExpectedException('OpenConext\\Component\\EngineBlockMetadata\\MetadataRepository\\EntityNotFoundException');

        $repository = new CachedDoctrineMetadataRepository($doctrineRepository);
        $repository->fetchIdentityProviderByEntityId('test');
    }

    public function testFetchServiceProviderThrowExceptions()
    {
        $doctrineRepository = Mockery::mock('OpenConext\Component\EngineBlockMetadata\MetadataRepository\DoctrineMetadataRepository');
        $doctrineRepository->shouldReceive('findServiceProviderByEntityId');

        $this->setExpectedException('OpenConext\\Component\\EngineBlockMetadata\\MetadataRepository\\EntityNotFoundException');

        $repository = new CachedDoctrineMetadataRepository($doctrineRepository);
        $repository->fetchServiceProviderByEntityId('test');
    }
}
