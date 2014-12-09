<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Interface FilterInterface
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
interface FilterInterface
{
    /**
     * @param AbstractRole $role
     * @return AbstractRole|null
     */
    public function filterRole(AbstractRole $role);

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder);

    /**
     * @return Expression
     */
    public function toExpression();

    /**
     * @return string
     */
    public function __toString();
}
