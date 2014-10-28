<?php

namespace OpenConext\Component\EngineBlockMetadata;

/**
 * A SAML2 metadata logo.
 * @package OpenConext\Component\EngineBlockMetadata
 */
class Logo
{
    public $height = null;
    public $width = null;
    public $url = null;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }
}
