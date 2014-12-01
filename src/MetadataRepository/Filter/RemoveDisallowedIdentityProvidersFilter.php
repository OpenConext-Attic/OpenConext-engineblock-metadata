<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;

/**
 * Class RemoveDisallowedIdentityProvidersFilter
 *
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class RemoveDisallowedIdentityProvidersFilter extends AbstractDisallowedIdentityProviderFilter
{
    /**
     * @param IdentityProvider $entity
     * @return mixed
     */
    protected function onDisallowedIdentityProvider(IdentityProvider $entity)
    {
        return null;
    }
}
