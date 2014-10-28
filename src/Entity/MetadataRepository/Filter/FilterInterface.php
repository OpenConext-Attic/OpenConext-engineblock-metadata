<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;

/**
 * Interface FilterInterface
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
interface FilterInterface
{
    /**
     * @param AbstractConfigurationEntity $entity
     * @return AbstractConfigurationEntity
     */
    public function filter(AbstractConfigurationEntity $entity);

    public function __toString();
}
