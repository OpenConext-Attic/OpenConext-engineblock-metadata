<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

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
}
