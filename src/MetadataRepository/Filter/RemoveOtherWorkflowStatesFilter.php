<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;

/**
 * Class RemoveOtherWorkflowStatesFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class RemoveOtherWorkflowStatesFilter extends AbstractFilter
{
    /**
     * @var string
     */
    private $workflowState;

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function __construct(ServiceProvider $serviceProvider)
    {
        $this->workflowState = $serviceProvider->workflowState;
    }

    /**
     * {@inheritdoc}
     */
    public function filterRole(AbstractRole $role)
    {
        return $role->workflowState === $this->workflowState ? $role : null;
    }

    /**
     * {@inheritdoc}
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere('role.workflowState <> :bannedWorkflowState')
            ->setParameter('bannedWorkflowState', $this->workflowState);
    }

    /**
     * {@inheritdoc}
     */
    public function toExpression($repositoryClassName)
    {
        return Criteria::expr()->neq('workflowState', $this->workflowState);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->workflowState;
    }
}
