<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

class ServiceProviderEntity extends AbstractConfigurationEntity
{
    public $assertionConsumerServices;

    public $transparentIssuer = false;
    public $implicitVoId;
    public $displayUnconnectedIdpsWayf;
    public $requestsMustBeSigned = false;
    public $noConsentRequired = false;
    public $eula;
    public $provideIsMemberOf;
    public $skipDenormalization = false;
    public $allowedIdpEntityIds = array();
}