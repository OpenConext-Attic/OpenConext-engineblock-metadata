<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

/**
 * Class AbstractFilter
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
}
