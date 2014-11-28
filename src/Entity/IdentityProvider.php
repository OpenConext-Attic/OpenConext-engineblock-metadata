<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\ShibMdScope;
use OpenConext\Component\EngineBlockMetadata\Service;

/**
 * Class IdentityProvider
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class IdentityProvider extends AbstractConfigurationEntity
{
    const GUEST_QUALIFIER_ALL = 'All';
    const GUEST_QUALIFIER_SOME = 'Some';
    const GUEST_QUALIFIER_NONE = 'None';

    /**
     * In all-caps to indicate that though the language doesn't allow it, this should be an array constant.
     *
     * @var string[]
     */
    public $GUEST_QUALIFIERS = array(self::GUEST_QUALIFIER_ALL, self::GUEST_QUALIFIER_SOME, self::GUEST_QUALIFIER_NONE);

    /**
     * @var bool
     */
    public $enabledInWayf = true;

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
    public $schacHomeOrganization = null;

    /**
     * @var string[]
     */
    public $spsEntityIdsWithoutConsent = array();

    /**
     * @var bool
     */
    public $hidden = false;

    /**
     * @var ShibMdScope[]
     */
    public $shibMdScopes = array();
}
