<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

abstract class AbstractFilter implements FilterInterface
{
    public function __toString()
    {
        return get_class($this);
    }
}