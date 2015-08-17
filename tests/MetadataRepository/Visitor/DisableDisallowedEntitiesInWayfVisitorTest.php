<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;

/**
 * Class DisableDisallowedEntitiesInWayfVisitorTest
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor
 */
class DisableDisallowedEntitiesInWayfVisitorTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitor()
    {
        $vistor = new DisableDisallowedEntitiesInWayfVisitor(array(
            'https://enabled.entity.com',
        ));
        $disabledIdentityProvider = new IdentityProvider('https://disabled1.entity.com');
        $this->assertTrue($disabledIdentityProvider->enabledInWayf);
        $vistor->visitIdentityProvider($disabledIdentityProvider);
        $this->assertFalse($disabledIdentityProvider->enabledInWayf);

        $enabledIdentityProvider = new IdentityProvider('https://enabled.entity.com');
        $this->assertTrue($enabledIdentityProvider->enabledInWayf);
        $vistor->visitIdentityProvider($enabledIdentityProvider);
        $this->assertTrue($enabledIdentityProvider->enabledInWayf);
    }
}
