<?php

namespace OpenConext\Component\EngineBlockMetadata;

class IndexedService extends Service
{
    /**
     * @var int
     */
    public $serviceIndex;

    /**
     * @var bool|null
     */
    public $isDefault = null;
}