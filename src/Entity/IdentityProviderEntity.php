<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

class IdentityProviderEntity extends AbstractConfigurationEntity
{
    const GUEST_QUALIFIER_ALL = 'All';
    const GUEST_QUALIFIER_SOME = 'Some';
    const GUEST_QUALIFIER_NONE = 'None';

    public $inWayf = false;

    public $assertionConsumerServices = array();

    public $singleSignOnServices = array();
    public $guestQualifier = self::GUEST_QUALIFIER_ALL;
    public $schacHomeOrganization;
    public $spsWithoutConsent = array();
    public $hidden = false;
    public $shibMdScopes = array();
    public $allowedServiceProviderEntityIds = array();
}