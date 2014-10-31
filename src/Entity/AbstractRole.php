<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\Service;
use SAML2_Certificate_X509;
use SAML2_Const;

/**
 * @ORM\Entity
 * @ORM\Table(name="sso_role")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "sp"  = "OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider",
 *  "idp" = "OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider"
 * })
 */
abstract class AbstractRole
{
    const WORKFLOW_STATE_PROD = 'prodaccepted';
    const WORKFLOW_STATE_TEST = 'testaccepted';
    const WORKFLOW_STATE_DEFAULT = self::WORKFLOW_STATE_PROD;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_id", type="string")
     */
    protected $entityId;

    /**
     * @var string
     * @ORM\Column(name="name_nl", type="string")
     */
    protected $nameNl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="name_en", type="string")
     */
    protected $nameEn = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description_nl", type="string")
     */
    protected $descriptionNl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description_en", type="string")
     */
    protected $descriptionEn = '';

    /**
     * @var string
     *
     * @ORM\Column(name="display_name_nl", type="string")
     */
    protected $displayNameNl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="display_name_en", type="string")
     */
    protected $displayNameEn = '';

    /**
     * @var null|int
     *
     * @ORM\Column(name="logo_height_px", type="integer")
     */
    protected $logoHeightPx = null;

    /**
     * @var null|int
     *
     * @ORM\Column(name="logo_width_px", type="integer")
     */
    protected $logoWidthPx = null;

    /**
     * @var null|string
     *
     * @ORM\Column(name="logo_url", type="string")
     */
    protected $logoUrl = null;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_nl_name",type="string")
     */
    protected $organizationNlName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_nl_display_name",type="string")
     */
    protected $organizationNlDisplayName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_nl_url",type="string")
     */
    protected $organizationNlUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_en_name",type="string")
     */
    protected $organizationEnName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_en_display_name",type="string")
     */
    protected $organizationEnDisplayName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization_en_url",type="string")
     */
    protected $organizationEnUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords_nl", type="string")
     */
    protected $keywordsNl = '';

    /**
     * @var string
     *
     * @ORM\Column(name="keywords_en", type="string")
     */
    protected $keywordsEn = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="publish_in_edugain", type="boolean")
     */
    protected $publishInEdugain = false;

    /**
     * @var SAML2_Certificate_X509[]
     *
     * @ORM\Column(name="certificates", type="x509_certificates_collection")
     */
    protected $certificates = array();

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_state", type="string")
     */
    protected $workflowState = self::WORKFLOW_STATE_DEFAULT;

    /**
     * @var ContactPerson[]
     *
     * @ORM\OneToMany(
     *  targetEntity="OpenConext\Component\EngineBlockMetadata\Entity\Role\ContactPerson",
     *  mappedBy="role"
     * )
     */
    protected $contactPersons = array();

    /**
     * @var string
     *
     * @ORM\Column(name="name_id_format", type="string")
     */
    protected $nameIdFormat = null;

    /**
     * @var string[]
     */
    protected $nameIdFormats = array(
        SAML2_Const::NAMEID_TRANSIENT,
        SAML2_Const::NAMEID_PERSISTENT,
    );

    /**
     * @var string
     *
     * @ORM\Column(name="single_logout_service_binding", type="string")
     */
    protected $singleLogoutServiceBinding;

    /**
     * @var string
     *
     * @ORM\Column(name="single_logout_service_location", type="string")
     */
    protected $singleLogoutServiceLocation;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="publish_in_edu_gain_date", type="datetime")
     */
    protected $publishInEduGainDate = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="disable_scoping", type="boolean")
     */
    protected $disableScoping = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="additional_logging", type="boolean")
     */
    protected $additionalLogging = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="requests_must_be_signed", type="boolean")
     */
    protected $requestsMustBeSigned = false;

    /**
     * @var string
     *
     * @ORM\Column(name="response_processing_service_binding", type="string")
     */
    protected $responseProcessingServiceBinding = null;

    /**
     * @var string
     *
     * @ORM\Column(name="response_processing_service_location", type="string")
     */
    protected $responseProcessingServiceLocation = null;

    /**
     * @param $entityId
     */
    protected function __construct($entityId)
    {
        $this->entityId = $entityId;
    }
}
