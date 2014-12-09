<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Class RemoveEntityByEntityId
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class RemoveEntityByEntityId extends AbstractFilter
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function filterRole(AbstractRole $role)
    {
        return $role->entityId === $this->entityId ? null : $role;
    }

    /**
     * {@inheritdoc}
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->andWhere('entityId <> :removeEntityId')
            ->setParameter('removeEntityId', $this->entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function toExpression()
    {
        return Criteria::expr()->neq('entityId', $this->entityId);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->entityId;
    }
}
