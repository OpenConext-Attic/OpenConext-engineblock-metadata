<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\ORM\QueryBuilder;
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

    /**
     * @param QueryBuilder $queryBuilder
     * @return null
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
    }

    /**
     *
     */
    public function toCriteria()
    {
    }
}
