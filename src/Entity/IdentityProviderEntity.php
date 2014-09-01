<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Configuration\ShibMdScope;
use OpenConext\Component\EngineBlockMetadata\Service;

class IdentityProviderEntity extends AbstractConfigurationEntity
{
    const GUEST_QUALIFIER_ALL = 'All';
    const GUEST_QUALIFIER_SOME = 'Some';
    const GUEST_QUALIFIER_NONE = 'None';

    /**
     * @var string[]
     */
    public $GUEST_QUALIFIERS = array(self::GUEST_QUALIFIER_ALL, self::GUEST_QUALIFIER_SOME, self::GUEST_QUALIFIER_NONE);

    /**
     * @var bool
     */
    public $inWayf = true;

    /**
     * @var Service[]
     */
    public $singleSignOnServices = array();

    /**
     * @var string
     */
    public $guestQualifier = self::GUEST_QUALIFIER_ALL;

    /**
     * @var string
     */
    public $schacHomeOrganization;

    /**
     * @var string[]
     */
    public $spsWithoutConsent = array();

    /**
     * @var bool
     */
    public $hidden = false;

    /**
     * @var ShibMdScope[]
     */
    public $shibMdScopes = array();

    /**
     * @var string[]
     */
    public $allowedServiceProviderEntityIds = array();
}