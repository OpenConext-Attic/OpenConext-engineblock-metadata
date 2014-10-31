<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;

/**
 * Class DisableDisallowedEntitiesInWayfFilter
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
class DisableDisallowedEntitiesInWayfFilter extends AbstractDisallowedIdentityProviderFilter
{
    /**
     * @param IdentityProviderEntity $entity
     * @return mixed
     */
    protected function onDisallowedIdentityProvider(IdentityProviderEntity $entity)
    {
        $entity->enabledInWayf = false;
        return $entity;
    }
}
