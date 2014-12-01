<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Interface FilterInterface
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
interface FilterInterface
{
    /**
     * @param AbstractRole $entity
     * @return AbstractRole
     */
    public function filter(AbstractRole $entity);

    public function __toString();

    /**
     * @param QueryBuilder $queryBuilder
     * @return mixed
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder);

    public function toCriteria();
}
