<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;

/**
 * Class DisableDisallowedEntitiesInWayfFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class DisableDisallowedEntitiesInWayfFilter extends AbstractDisallowedIdentityProviderFilter
{
    /**
     * @param IdentityProvider $entity
     * @return mixed
     */
    protected function onDisallowedIdentityProvider(IdentityProvider $entity)
    {
        $entity->enabledInWayf = false;
        return $entity;
    }
}
