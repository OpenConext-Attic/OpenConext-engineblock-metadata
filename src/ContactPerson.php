<?php

namespace OpenConext\Component\EngineBlockMetadata;

class ContactPerson
{
    public $contactType;
    public $emailAddress;
    public $givenName;
    public $surName;

    public function __construct($contactType)
    {
        $this->contactType = $contactType;
    }
}