<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use Exception;
use InvalidArgumentException;
use Mockery;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\Utils;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

/**
 * Class MetadataRepositoryTest
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository
 */
class MetadataRepositoryTest extends PHPUnit_Framework_TestCase
{
    const MOCK_IDP_NAME = 'https://idp.example.edu';

    public function testBreakOnInvalidIdentityProvider()
    {
        $this->setExpectedException(get_class(new PHPUnit_Framework_Error("",0,"",1)));

        new InMemoryMetadataRepository(
            array(array('EntityID'=>'http://dummy')),
            array()
        );
    }

    public function testBreakOnInvalidServiceProvider()
    {
        $this->setExpectedException(get_class(new PHPUnit_Framework_Error("",0,"",1)));

        new InMemoryMetadataRepository(
            array(),
            array(array('EntityID'=>'http://dummy'))
        );
    }

    public function testCreateFromConfigStartsEmpty()
    {
        $repository = InMemoryMetadataRepository::createFromConfig(
            array(),
            Mockery::mock('OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface')
        );
        $this->assertEmpty($repository->findIdentityProviders());
    }

    public function testFetchEntityByEntityId()
    {
        $repository = new InMemoryMetadataRepository(array(), array());

        $e = null;
        try {
            $repository->fetchEntityByEntityId(self::MOCK_IDP_NAME);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $repository->registerServiceProvider(new ServiceProvider('https://sp.example.edu'));
        $this->assertNotNull($repository->fetchEntityByEntityId('https://sp.example.edu'));
    }

    public function testFetchIdentityProviderByEntityId()
    {
        $repository = new InMemoryMetadataRepository(array(), array());

        $e = null;
        try {
            $repository->fetchIdentityProviderByEntityId(self::MOCK_IDP_NAME);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);

        $repository->registerIdentityProvider(new IdentityProvider(self::MOCK_IDP_NAME));
        $this->assertNotNull($repository->fetchIdentityProviderByEntityId(self::MOCK_IDP_NAME));
    }

    public function testFetchServiceProviderByEntityId()
    {
        $repository = new InMemoryMetadataRepository(array(), array());

        $e = null;
        try {
            $repository->fetchServiceProviderByEntityId('https://sp.example.edu');
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $repository->registerServiceProvider(new ServiceProvider('https://sp.example.edu'));
        $this->assertNotNull($repository->fetchServiceProviderByEntityId('https://sp.example.edu'));
    }

    public function testFindServiceProvider()
    {
        $sp = new ServiceProvider('https://entityId');
        $repository = new InMemoryMetadataRepository(array(), array($sp));
        $this->assertEquals($sp, $repository->findServiceProviderByEntityId('https://entityId'));
        $this->assertNull($repository->findServiceProviderByEntityId('https://404.example.edu'));
    }

    public function testFindIdentityProvider()
    {
        $idp = new IdentityProvider('https://entityId');
        $repository = new InMemoryMetadataRepository(array($idp), array());
        $this->assertEquals($idp, $repository->findIdentityProviderByEntityId('https://entityId'));
        $this->assertNull($repository->findIdentityProviderByEntityId('https://404.example.edu'));

        $idps = $repository->findIdentityProviders();
        $this->assertCount(1, $idps);
        $this->assertEquals($idp, $idps[$idp->entityId]);
    }

    public function testFindEntitiesPublishableInEdugain()
    {
        $repository = $this->getFilledRepository();

        $publishable = $repository->findEntitiesPublishableInEdugain();
        $this->assertCount(1, $publishable);
        $this->assertEquals('https://idp1.example.edu', $publishable[0]->entityId);
    }

    public function testFindReservedSchacHomeOrganizations()
    {
        $repository = $this->getFilledRepository();

        $this->assertEquals(array('idp1.example.edu'), $repository->findReservedSchacHomeOrganizations());
    }

    public function testRegisterEntities()
    {
        $repository = new InMemoryMetadataRepository(array(), array());
        $this->assertEmpty($repository->findIdentityProviders());
        $this->assertNull($repository->findServiceProviderByEntityId('https://some.sp.example.edu'));

        $repository->registerIdentityProvider(new IdentityProvider('https://some.idp.example.edu'));
        $this->assertCount(1, $repository->findIdentityProviders());

        $repository->registerServiceProvider(new ServiceProvider('https://some.sp.example.edu'));
        $this->assertNotNull($repository->findServiceProviderByEntityId('https://some.sp.example.edu'));
    }

    public function testFilterApplication()
    {
        $repository = $this->getFilledRepository();

        $mockFilter = Mockery::mock(
            'OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface'
        );
        $mockFilter->shouldReceive('filterRole')->andReturnNull();
        $repository->appendFilter($mockFilter);

        $this->assertEmpty($repository->findIdentityProviders());
        $this->assertEmpty($repository->findAllIdentityProviderEntityIds());
        $this->assertEmpty($repository->findIdentityProvidersByEntityId(array('https://idp2.example.edu')));

        $this->assertNull($repository->findEntityByEntityId('https://idp1.example.edu'));
        $this->assertNull($repository->findIdentityProviderByEntityId('https://idp1.example.edu'));
        $this->assertNull($repository->findServiceProviderByEntityId('https://sp1.example.edu'));

        $this->assertEmpty($repository->findReservedSchacHomeOrganizations());

        // Make sure the filter is also applied to entity roles added after the filter has been registered.
        $repository->registerIdentityProvider(new IdentityProvider('https://idp4.example.edu'));
        $this->assertNull($repository->findIdentityProviderByEntityId('https://idp4.example.edu'));
    }

    public function testVisitorApplication()
    {
        $repository = $this->getFilledRepository();

        $visitor = Mockery::mock(
            'OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface'
        );
        $visitor->shouldReceive('visitIdentityProvider')->andReturnUsing(
            function (IdentityProvider $idp) {
                $idp->nameEn = 'MOCKED';
            }
        );

        $repository->appendVisitor($visitor);

        $identityProviders = $repository->findIdentityProviders();
        $this->assertCount(3, $identityProviders);
        foreach ($identityProviders as $identityProvider) {
            $this->assertEquals('MOCKED', $identityProvider->nameEn);
        }
        $identityProviders = $repository->findIdentityProvidersByEntityId(array('https://idp2.example.edu'));
        $this->assertCount(1, $identityProviders);
        $this->assertEquals('MOCKED', reset($identityProviders)->nameEn);

        $identityProvider = $repository->findEntityByEntityId('https://idp3.example.edu');
        $this->assertEquals('MOCKED', $identityProvider->nameEn);
    }

    /**
     * @return InMemoryMetadataRepository
     */
    private function getFilledRepository()
    {
        $repository = new InMemoryMetadataRepository(
            array(
                Utils::instantiate(
                    'OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider',
                    array(
                        'entityId' => 'https://idp1.example.edu',
                        'publishInEdugain'=>true,
                        'schacHomeOrganization'=> 'idp1.example.edu'
                    )
                ),
                new IdentityProvider('https://idp2.example.edu'),
                new IdentityProvider('https://idp3.example.edu'),
            ),
            array(
                new ServiceProvider('https://sp1.example.edu'),
                new ServiceProvider('https://sp2.example.edu'),
                new ServiceProvider('https://sp3.example.edu'),
            )
        );
        return $repository;
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
