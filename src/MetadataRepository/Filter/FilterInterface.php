<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use Psr\Log\LoggerInterface;

/**
 * Interface FilterInterface
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
interface FilterInterface
{
    /**
     * @param AbstractRole $role
     * @param LoggerInterface|null $logger
     * @return null|AbstractRole
     */
    public function filterRole(AbstractRole $role, LoggerInterface $logger = null);

    /**
     * @param string $repositoryClassName
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder, $repositoryClassName);

    /**
     * @param string $repositoryClassName
     * @return Expression|null
     */
    public function toExpression($repositoryClassName);

    /**
     * @return string
     */
    public function __toString();
}
