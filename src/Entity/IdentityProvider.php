<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenConext\Component\EngineBlockMetadata\Service;
use OpenConext\Component\EngineBlockMetadata\ShibMdScope;

/**
 * @ORM\Entity
 * @ORM\Table(name="identity_provider")
 */
class IdentityProvider extends AbstractRole
{
    const GUEST_QUALIFIER_ALL = 'All';
    const GUEST_QUALIFIER_SOME = 'Some';
    const GUEST_QUALIFIER_NONE = 'None';

    /**
     * In all-caps to indicate that though the language doesn't allow it, this should be an array constant.
     *
     * @var string[]
     */
    protected $GUEST_QUALIFIERS = array(
        self::GUEST_QUALIFIER_ALL,
        self::GUEST_QUALIFIER_SOME,
        self::GUEST_QUALIFIER_NONE
    );

    /**
     * @var bool
     */
    protected $enabledInWayf = true;

    /**
     * @var Service[]
     */
    protected $singleSignOnServices = array();

    /**
     * @var string
     */
    protected $guestQualifier = self::GUEST_QUALIFIER_ALL;

    /**
     * @var string
     */
    protected $schacHomeOrganization = null;

    /**
     * @var string[]
     */
    protected $spsEntityIdsWithoutConsent = array();

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var ShibMdScope[]
     */
    protected $shibMdScopes = array();
}
