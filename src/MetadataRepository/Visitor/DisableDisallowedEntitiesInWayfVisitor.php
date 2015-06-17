<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor;

use BadMethodCallException;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Class DisableDisallowedEntitiesInWayfVisitor
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor
 */
class DisableDisallowedEntitiesInWayfVisitor implements VisitorInterface
{
    /**
     * @var array
     */
    private $allowedEntityIds;

    /**
     * @param $allowedEntityIds
     */
    public function __construct(array $allowedEntityIds)
    {
        $this->allowedEntityIds = $allowedEntityIds;
    }

    /**
     * {@inheritdoc}
     */
    public function visitIdentityProvider(IdentityProvider $identityProvider)
    {
        if (in_array($identityProvider->entityId, $this->allowedEntityIds)) {
            return;
        }

        $identityProvider->enabledInWayf = false;
    }

    /**
     * {@inheritdoc}
     */
    public function visitServiceProvider(ServiceProvider $serviceProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function visitRole(AbstractRole $role)
    {
    }
}
