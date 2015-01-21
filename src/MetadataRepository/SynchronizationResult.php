<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

class SynchronizationResult
{
    public $success = true;
    public $createdServiceProviders = array();
    public $createdIdentityProviders = array();
    public $updatedServiceProviders = array();
    public $updatedIdentityProviders = array();
    public $removedServiceProviders = array();
    public $removedIdentityProviders = array();
}
