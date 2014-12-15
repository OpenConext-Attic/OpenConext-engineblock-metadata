<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Mockery;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use PHPUnit_Framework_TestCase;

/**
 * Class DoctrineMetadataRepositoryTest
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 */
class DoctrineMetadataRepositoryTest extends PHPUnit_Framework_TestCase
{
    public function testFindIdentityProviders()
    {
        $mockSpRepository = Mockery::mock('Doctrine\ORM\EntityRepository');
        $mockIdpRepository = Mockery::mock('Doctrine\ORM\EntityRepository');
        $mockIdpRepository
            ->shouldReceive('matching')
            ->andReturn(new ArrayCollection(array(new IdentityProvider('https://idp.entity.com'))));

        $repository = new DoctrineMetadataRepository($mockSpRepository, $mockIdpRepository);

        $this->assertCount(1, $repository->findIdentityProviders());
    }
}
