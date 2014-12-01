<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\AST\InExpression;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere("entityId IN(:allowedEntityIds)")
            ->setParameter('allowedEntityIds', $this->allowedIdentityProviderEntityIds);
    }

    /**
     * @return Criteria
     */
    public function toCriteria()
    {
        return Criteria::create()->where(Criteria::expr()->in('entityId', $this->allowedIdentityProviderEntityIds));
    }
}
