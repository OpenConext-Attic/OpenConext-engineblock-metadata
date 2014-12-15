<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use PHPUnit_Framework_TestCase;

/**
 * Class IdentityProviderTest
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 */
class IdentityProviderTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $entityId = 'https://idp.example.edu';
        $idp = new IdentityProvider($entityId);
        $this->assertEquals($entityId, $idp->entityId);
    }
}
