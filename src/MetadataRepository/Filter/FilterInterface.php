<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\EntityRepository;
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
     * @param string $repositoryClassName
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder, $repositoryClassName);

    /**
     * @param string $repositoryClassName
     * @return Expression|NULL
     */
    public function toExpression($repositoryClassName);

    /**
     * @return string
     */
    public function __toString();
}
