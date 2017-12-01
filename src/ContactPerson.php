<?php

namespace OpenConext\Component\EngineBlockMetadata;

/**
 * Class ContactPerson
 * @package OpenConext\Component\EngineBlockMetadata
 */
class ContactPerson
{
    public $contactType;
    public $emailAddress = '';
    public $telephoneNumber = '';
    public $givenName = '';
    public $surName = '';

    /**
     * @param $contactType
     */
    public function __construct($contactType)
    {
        $this->contactType = $contactType;
    }
}
