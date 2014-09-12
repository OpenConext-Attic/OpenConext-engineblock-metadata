<?php

namespace OpenConext\Component\EngineBlockMetadata;

class Organization
{
    public $name;
    public $displayName;
    public $url;

    public function __construct($name, $displayName, $url)
    {
        $this->displayName = $displayName;
        $this->name = $name;
        $this->url = $url;
    }
}