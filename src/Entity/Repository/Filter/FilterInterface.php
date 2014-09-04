<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Repository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;

interface FilterInterface
{
    /**
     * @param AbstractConfigurationEntity $entity
     * @return AbstractConfigurationEntity
     */
    public function filter(AbstractConfigurationEntity $entity);
}