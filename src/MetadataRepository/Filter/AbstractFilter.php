<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
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
