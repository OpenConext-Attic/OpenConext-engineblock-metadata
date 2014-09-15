<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;


/**
 * Class RemoveDisallowedIdentityProvidersFilter
 *
 * @deprecated this is a bad idea
 *
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
class RemoveDisallowedIdentityProvidersFilter extends AbstractDisallowedIdentityProviderFilter
{
    protected function onDisallowedIdentityProvider(IdentityProviderEntity $entity)
    {
        return null;
    }
}