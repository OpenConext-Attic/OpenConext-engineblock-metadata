<?php

namespace OpenConext\Component\EngineBlockMetadata;

class IndexedService extends Service
{
    /**
     * @var int
     */
    public $serviceIndex;

    /**
     * Note that NULL and FALSE are NOT the same in this context.
     *
     * @var bool|null
     */
    public $isDefault = null;

    function __construct($location, $binding, $serviceIndex, $isDefault = null)
    {
        $this->isDefault = $isDefault;
        $this->serviceIndex = $serviceIndex;
        $this->isDefault = $isDefault;
        parent::__construct($location, $binding);
    }
}