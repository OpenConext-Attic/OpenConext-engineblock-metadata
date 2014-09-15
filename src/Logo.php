<?php

namespace OpenConext\Component\EngineBlockMetadata;

class Logo
{
    public $height = null;
    public $width = null;
    public $url = null;

    public function __construct($url)
    {
        $this->url = $url;
    }
}