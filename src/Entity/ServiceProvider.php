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
use Doctrine\ORM;

/**
 * Class ServiceProvider
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 *
 * @ORM\Mapping\MappedSuperclass()
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ServiceProvider extends AbstractRole
{
    /**
     * @var IndexedService[]
     *
     * @ORM\Column(name="assertion_consumer_services", type="array")
     */
    public $assertionConsumerServices = array();

    /**
     * @var bool
     *
     * @ORM\Column(name="is_transparent_issuer", type="boolean")
     */
    public $isTransparentIssuer = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_trusted_proxy", type="boolean")
     */
    public $isTrustedProxy = false;

    /**
     * @var string
     *
     * @ORM\Column(name="implicit_vo_id", type="string")
     */
    public $implicitVoId = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="display_unconnected_idps_wayf", type="boolean")
     */
    public $displayUnconnectedIdpsWayf = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_consent_required", type="boolean")
     */
    public $isConsentRequired = true;

    /**
     * @var string
     *
     * @ORM\Column(name="eula", type="string")
     */
    public $eula = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="skip_denormalization", type="boolean")
     */
    public $skipDenormalization = false;

    /**
     * @var array
     *
     * @ORM\Column(name="allowed_idp_entity_ids", type="array")
     */
    public $allowedIdpEntityIds = array();

    /**
     * @var null|RequestedAttribute[]
     *
     * @ORM\Column(name="requested_attributes", type="array")
     */
    public $requestedAttributes = null;

    /**
     * @var string
     *
     * @ORM\Column(name="manipulation", type="text")
     */
    protected $manipulation;

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
        $nameIdFormats = array(
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
        $eula = null,
        $implicitVoId = null,
        $isConsentRequired = true,
        $isTransparentIssuer = false,
        $isTrustedProxy = false,
        $requestedAttributes = null,
        $skipDenormalization = false
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
            $nameIdFormats,
            $publishInEduGainDate,
            $publishInEdugain,
            $requestsMustBeSigned,
            $responseProcessingService,
            $workflowState
        );

        $this->allowedIdpEntityIds = $allowedIdpEntityIds;
        $this->assertionConsumerServices = $assertionConsumerServices;
        $this->displayUnconnectedIdpsWayf = $displayUnconnectedIdpsWayf;
        $this->eula = $eula;
        $this->implicitVoId = $implicitVoId;
        $this->isConsentRequired = $isConsentRequired;
        $this->isTransparentIssuer = $isTransparentIssuer;
        $this->isTrustedProxy = $isTrustedProxy;
        $this->requestedAttributes = $requestedAttributes;
        $this->skipDenormalization = $skipDenormalization;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitServiceProvider($this);
    }
}
