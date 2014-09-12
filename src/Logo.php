<?php

namespace OpenConext\Component\EngineBlockMetadata;

class Logo
{
    public $height;
    public $width;
    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }
}