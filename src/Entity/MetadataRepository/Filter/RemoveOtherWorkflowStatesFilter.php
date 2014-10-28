<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

/**
 * Class RemoveOtherWorkflowStatesFilter
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
class RemoveOtherWorkflowStatesFilter extends AbstractFilter
{
    private $workflowState;

    /**
     * @param ServiceProviderEntity $serviceProvider
     */
    public function __construct(ServiceProviderEntity $serviceProvider)
    {
        $this->workflowState = $serviceProvider->workflowState;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @return AbstractConfigurationEntity
     */
    public function filter(AbstractConfigurationEntity $entity)
    {
        return $entity->workflowState === $this->workflowState ? $entity : null;
    }

    /**
     * @return mixed
     */
    public function getWorkflowState()
    {
        return $this->workflowState;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->workflowState;
    }
}
