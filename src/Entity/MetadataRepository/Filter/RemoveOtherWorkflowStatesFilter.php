<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class RemoveOtherWorkflowStatesFilter extends AbstractFilter
{
    private $workflowState;

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

    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->workflowState;
    }
}