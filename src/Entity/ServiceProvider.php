<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface;
use OpenConext\Component\EngineBlockMetadata\Organization;
use OpenConext\Component\EngineBlockMetadata\RequestedAttribute;
use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\Service;
use SAML2_Const;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ServiceProvider
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ServiceProvider extends AbstractRole
{
    /**
     * @var null|AttributeReleasePolicy
     *
     * @ORM\Column(name="attribute_release_policy", type="array")
     */
    protected $attributeReleasePolicy;

    /**
     * @var IndexedService[]
     *
     * @ORM\Column(name="assertion_consumer_services", type="array")
     */
    public $assertionConsumerServices;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_transparent_issuer", type="boolean")
     */
    public $isTransparentIssuer;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_trusted_proxy", type="boolean")
     */
    public $isTrustedProxy;

    /**
     * @var string
     *
     * @ORM\Column(name="implicit_vo_id", type="string")
     */
    public $implicitVoId;

    /**
     * @var bool
     *
     * @ORM\Column(name="display_unconnected_idps_wayf", type="boolean")
     */
    public $displayUnconnectedIdpsWayf;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_consent_required", type="boolean")
     */
    public $isConsentRequired;

    /**
     * @var string
     *
     * @ORM\Column(name="terms_of_service_url", type="string")
     */
    public $termsOfServiceUrl;

    /**
     * @var bool
     *
     * @ORM\Column(name="skip_denormalization", type="boolean")
     */
    public $skipDenormalization;

    /**
     * @var string[]
     *
     * @ORM\Column(name="allowed_idp_entity_ids", type="array")
     */
    public $allowedIdpEntityIds;

    /**
     * @var bool
     *
     * @ORM\Column(name="policy_enforcement_decision_required", type="boolean")
     */
    public $policyEnforcementDecisionRequired;

    /**
     * @var null|RequestedAttribute[]
     *
     * @ORM\Column(name="requested_attributes", type="array")
     */
    public $requestedAttributes;

    /**
     * @var null|string
     *
     * @ORM\Column(name="url_en", type="string")
     */
    public $urlEn;

    /**
     * @var null|string
     *
     * @ORM\Column(name="url_nl", type="string")
     */
    public $urlNl;

    /**
     * @param string $entityId
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
     * @param array $allowedIdpEntityIds
     * @param array $assertionConsumerServices
     * @param bool $displayUnconnectedIdpsWayf
     * @param null $termsOfServiceUrl
     * @param null $implicitVoId
     * @param bool $isConsentRequired
     * @param bool $isTransparentIssuer
     * @param bool $isTrustedProxy
     * @param null $requestedAttributes
     * @param bool $skipDenormalization
     * @param bool $policyEnforcementDecisionRequired
     * @param string $manipulation
     * @param AttributeReleasePolicy $attributeReleasePolicy
     * @param string $urlEn
     * @param string $urlNl
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
        array $allowedIdpEntityIds = array(),
        array $assertionConsumerServices = array(),
        $displayUnconnectedIdpsWayf = false,
        $termsOfServiceUrl = null,
        $implicitVoId = null,
        $isConsentRequired = true,
        $isTransparentIssuer = false,
        $isTrustedProxy = false,
        $requestedAttributes = null,
        $skipDenormalization = false,
        $policyEnforcementDecisionRequired = false,
        $manipulation = '',
        AttributeReleasePolicy $attributeReleasePolicy = null,
        $urlEn = null,
        $urlNl = null
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
        $this->allowedIdpEntityIds = $allowedIdpEntityIds;
        $this->assertionConsumerServices = $assertionConsumerServices;
        $this->displayUnconnectedIdpsWayf = $displayUnconnectedIdpsWayf;
        $this->termsOfServiceUrl = $termsOfServiceUrl;
        $this->implicitVoId = $implicitVoId;
        $this->isConsentRequired = $isConsentRequired;
        $this->isTransparentIssuer = $isTransparentIssuer;
        $this->isTrustedProxy = $isTrustedProxy;
        $this->requestedAttributes = $requestedAttributes;
        $this->skipDenormalization = $skipDenormalization;
        $this->policyEnforcementDecisionRequired = $policyEnforcementDecisionRequired;
        $this->urlEn = $urlEn;
        $this->urlNl = $urlNl;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitServiceProvider($this);
    }

    /**
     * @return null|AttributeReleasePolicy
     */
    public function getAttributeReleasePolicy()
    {
        return $this->attributeReleasePolicy;
    }
}
