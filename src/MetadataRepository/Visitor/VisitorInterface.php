<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Interface VisitorInterface
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor
 */
interface VisitorInterface
{
    /**
     * @param IdentityProvider $identityProvider
     * @return null
     */
    public function visitIdentityProvider(IdentityProvider $identityProvider);

    /**
     * @param ServiceProvider $serviceProvider
     * @return mixed
     */
    public function visitServiceProvider(ServiceProvider $serviceProvider);

    /**
     * @param AbstractRole $role
     * @return mixed
     */
    public function visitRole(AbstractRole $role);
}
