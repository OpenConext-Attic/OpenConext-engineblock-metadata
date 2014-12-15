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
     * @return IdentityProvider|null
     */
    public function visitIdentityProvider(IdentityProvider $identityProvider);

    /**
     * @param ServiceProvider $serviceProvider
     * @return ServiceProvider|null
     */
    public function visitServiceProvider(ServiceProvider $serviceProvider);

    /**
     * @param AbstractRole $role
     * @return AbstractRole|null
     */
    public function visitRole(AbstractRole $role);
}
