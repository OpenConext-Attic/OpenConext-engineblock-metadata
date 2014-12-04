<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use Doctrine\ORM;
use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface;
use OpenConext\Component\EngineBlockMetadata\Organization;
use OpenConext\Component\EngineBlockMetadata\ShibMdScope;
use OpenConext\Component\EngineBlockMetadata\Service;
use SAML2_Const;

/**
 * Class IdentityProvider
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 *
 * @ORM\Mapping\MappedSuperclass()
 *
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
    public $GUEST_QUALIFIERS = array(self::GUEST_QUALIFIER_ALL, self::GUEST_QUALIFIER_SOME, self::GUEST_QUALIFIER_NONE);

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled_in_wayf", type="boolean")
     */
    public $enabledInWayf = true;

    /**
     * @var Service[]
     *
     * @ORM\Column(name="single_sign_on_services", type="array")
     */
    public $singleSignOnServices = array();

    /**
     * @var string
     *
     * @ORM\Column(name="guest_qualifier", type="string")
     */
    public $guestQualifier = self::GUEST_QUALIFIER_ALL;

    /**
     * @var string
     *
     * @ORM\Column(name="schac_home_organization", type="string")
     */
    public $schacHomeOrganization = null;

    /**
     * @var string[]
     *
     * @ORM\Column(name="sps_entity_ids_without_consent", type="array")
     */
    public $spsEntityIdsWithoutConsent = array();

    /**
     * @var bool
     *
     * @ORM\Column(name="hidden", type="boolean")
     */
    public $hidden = false;

    /**
     * @var ShibMdScope[]
     *
     * @ORM\Column(name="shib_md_scopes", type="array")
     */
    public $shibMdScopes = array();

    /**
     * @param $entityId
     * @param Organization $organizationEn
     * @param Organization $organizationNl
     * @param Service $singleLogoutService
     * @param bool $additionalLogging
     * @param array $certificates
     * @param array $contactPersons
     * @param string $descriptionEn
     * @param string $descriptionNl
     * @param bool $disableScoping
     * @param string $displayNameEn
     * @param string $displayNameNl
     * @param string $keywordsEn
     * @param string $keywordsNl
     * @param Logo $logo
     * @param string $nameEn
     * @param string $nameNl
     * @param null $nameIdFormat
     * @param array $supportedNameIdFormats
     * @param null $publishInEduGainDate
     * @param bool $publishInEdugain
     * @param bool $requestsMustBeSigned
     * @param Service $responseProcessingService
     * @param string $workflowState
     * @param bool $enabledInWayf
     * @param string $guestQualifier
     * @param bool $hidden
     * @param null $schacHomeOrganization
     * @param array $shibMdScopes
     * @param array $singleSignOnServices
     * @param array $spsEntityIdsWithoutConsent
     */
    public function __construct(
        $entityId,
        Organization $organizationEn = null,
        Organization $organizationNl = null,
        Service $singleLogoutService = null,
        $additionalLogging = false,
        array $certificates = array(),
        array $contactPersons = array(),
        $descriptionEn = '',
        $descriptionNl = '',
        $disableScoping = false,
        $displayNameEn = '',
        $displayNameNl = '',
        $keywordsEn = '',
        $keywordsNl = '',
        Logo $logo = null,
        $nameEn = '',
        $nameNl = '',
        $nameIdFormat = null,
        $supportedNameIdFormats = array(
            SAML2_Const::NAMEID_TRANSIENT,
            SAML2_Const::NAMEID_PERSISTENT,
        ),
        $publishInEduGainDate = null,
        $publishInEdugain = false,
        $requestsMustBeSigned = false,
        Service $responseProcessingService = null,
        $workflowState = self::WORKFLOW_STATE_DEFAULT,
        $manipulation = '',
        $attributeReleasePolicy = null,
        $enabledInWayf = true,
        $guestQualifier = self::GUEST_QUALIFIER_ALL,
        $hidden = false,
        $schacHomeOrganization = null,
        $shibMdScopes = array(),
        $singleSignOnServices = array(),
        $spsEntityIdsWithoutConsent = array()
    ) {
        parent::__construct(
            $entityId,
            $organizationEn,
            $organizationNl,
            $singleLogoutService,
            $additionalLogging,
            $certificates,
            $contactPersons,
            $descriptionEn,
            $descriptionNl,
            $disableScoping,
            $displayNameEn,
            $displayNameNl,
            $keywordsEn,
            $keywordsNl,
            $logo,
            $nameEn,
            $nameNl,
            $nameIdFormat,
            $supportedNameIdFormats,
            $publishInEduGainDate,
            $publishInEdugain,
            $requestsMustBeSigned,
            $responseProcessingService,
            $workflowState,
            $manipulation
        );

        $this->attributeReleasePolicy = $attributeReleasePolicy;
        $this->enabledInWayf = $enabledInWayf;
        $this->guestQualifier = $guestQualifier;
        $this->hidden = $hidden;
        $this->schacHomeOrganization = $schacHomeOrganization;
        $this->shibMdScopes = $shibMdScopes;
        $this->singleSignOnServices = $singleSignOnServices;
        $this->spsEntityIdsWithoutConsent = $spsEntityIdsWithoutConsent;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitIdentityProvider($this);
    }
}
