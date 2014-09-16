<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\Organization;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\Service;

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
     * @var \EngineBlock_X509_Certificate[]
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
        \EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_TRANSIENT,
        \EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_PERSISTENT,
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

    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }
}