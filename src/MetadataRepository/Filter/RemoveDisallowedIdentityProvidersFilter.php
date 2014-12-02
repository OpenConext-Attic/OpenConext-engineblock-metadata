<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;

/**
 * Class RemoveDisallowedIdentityProvidersFilter
 *
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class RemoveDisallowedIdentityProvidersFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $serviceProviderEntityId;

    /**
     * @var string[]
     */
    protected $allowedIdentityProviderEntityIds;

    /**
     * @param string $serviceProviderEntityId
     * @param array $allowedIdentityProviderEntityIds
     */
    public function __construct($serviceProviderEntityId, array $allowedIdentityProviderEntityIds)
    {
        $this->serviceProviderEntityId          = $serviceProviderEntityId;
        $this->allowedIdentityProviderEntityIds = $allowedIdentityProviderEntityIds;
    }

    /**
     * {@inheritdoc}
     */
    public function filterRole(AbstractRole $entity)
    {
        if (!$entity instanceof IdentityProvider) {
            return $entity;
        }

        if (in_array($entity->entityId, $this->allowedIdentityProviderEntityIds)) {
            return $entity;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->andWhere("entityId IN(:allowedEntityIds)")
            ->setParameter('allowedEntityIds', $this->allowedIdentityProviderEntityIds);
    }

    /**
     * {@inheritdoc}
     */
    public function toExpression()
    {
        return Criteria::expr()->in('entityId', $this->allowedIdentityProviderEntityIds);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->serviceProviderEntityId;
    }
}
