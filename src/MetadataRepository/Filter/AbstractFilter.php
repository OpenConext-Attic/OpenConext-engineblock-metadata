<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

/**
 * Class AbstractFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return get_class($this);
    }
}
