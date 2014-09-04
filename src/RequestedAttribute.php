<?php

namespace OpenConext\Component\EngineBlockMetadata\Configuration;

class RequestedAttribute
{
    const NAME_FORMAT_URI = 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $nameFormat = self::NAME_FORMAT_URI;

    /**
     * @var null|bool
     */
    public $required = null;

    public function __construct($name, $isRequired = false, $nameFormat = self::NAME_FORMAT_URI)
    {
        $this->name = $name;
        $this->nameFormat = $nameFormat;
        $this->required = $isRequired;
    }
}