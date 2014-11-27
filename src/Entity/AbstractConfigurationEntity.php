<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\Organization;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\Service;
use OpenConext\Component\EngineBlockMetadata\X509\X509Certificate;
use SAML2_Const;

/**
 * Abstract base class for configuration entities.
 *
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class AbstractConfigurationEntity
{
    const WORKFLOW_STATE_PROD = 'prodaccepted';
    const WORKFLOW_STATE_TEST = 'testaccepted';
    const WORKFLOW_STATE_DEFAULT = self::WORKFLOW_STATE_PROD;

    /**
     * @var string
     */
    public $entityId;

    /**
     * @var string
     */
    public $nameNl = '';

    /**
     * @var string
     */
    public $nameEn = '';

    /**
     * @var string
     */
    public $descriptionNl = '';

    /**
     * @var string
     */
    public $descriptionEn = '';

    /**
     * @var string
     */
    public $displayNameNl = '';

    /**
     * @var string
     */
    public $displayNameEn = '';

    /**
     * @var Logo
     */
    public $logo = null;

    /**
     * @var Organization
     */
    public $organizationNl = null;

    /**
     * @var Organization
     */
    public $organizationEn = null;

    /**
     * @var string
     */
    public $keywordsNl = '';

    /**
     * @var string
     */
    public $keywordsEn = '';

    /**
     * @var bool
     */
    public $publishInEdugain = false;

    /**
     * @var X509Certificate[]
     */
    public $certificates = array();

    /**
     * @var string
     */
    public $workflowState = self::WORKFLOW_STATE_DEFAULT;

    /**
     * @var ContactPerson[]
     */
    public $contactPersons = array();

    /**
     * @var string
     */
    public $nameIdFormat = null;

    /**
     * @var string[]
     */
    public $nameIdFormats = array(
        SAML2_Const::NAMEID_TRANSIENT,
        SAML2_Const::NAMEID_PERSISTENT,
    );

    /**
     * @var Service[]
     */
    public $singleLogoutServices = array();

    /**
     * @var \DateTime
     */
    public $publishInEduGainDate = null;

    /**
     * @var bool
     */
    public $disableScoping = false;

    /**
     * @var bool
     */
    public $additionalLogging = false;

    /**
     * @var bool
     */
    public $requestsMustBeSigned = false;

    /**
     * @var Service
     */
    public $responseProcessingService = null;

    /**
     * @param $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }
}
