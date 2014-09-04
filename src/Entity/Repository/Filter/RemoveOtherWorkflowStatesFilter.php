<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Repository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;

class RemoveOtherWorkflowStatesFilter implements FilterInterface
{
    private $workflowState;

    public function __construct($workflowState)
    {
        $this->workflowState = $workflowState;
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
}