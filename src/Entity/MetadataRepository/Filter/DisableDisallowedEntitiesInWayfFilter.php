<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;

class DisableDisallowedEntitiesInWayfFilter extends AbstractDisallowedIdentityProviderFilter
{
    protected function onDisallowedIdentityProvider(IdentityProviderEntity $entity)
    {
        $entity->enabledInWayf = false;
        return $entity;
    }
}