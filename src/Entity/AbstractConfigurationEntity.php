<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Configuration\Logo;
use OpenConext\Component\EngineBlockMetadata\Configuration\Organization;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\IndexedService;

abstract class AbstractConfigurationEntity
{
    const WORKFLOW_STATE_DEFAULT = 'production';

    /**
     * @var string
     */
    public $entityId;

    /**
     * @var string
     */
    public $nameNl;

    /**
     * @var string
     */
    public $nameEn;

    /**
     * @var string
     */
    public $descriptionNl;

    /**
     * @var string
     */
    public $descriptionEn;

    /**
     * @var string
     */
    public $displayNameNl;

    /**
     * @var string
     */
    public $displayNameEn;

    /**
     * @var  Logo
     */
    public $logo;

    /**
     * @var Organization
     */
    public $organizationNl;

    /**
     * @var Organization
     */
    public $organizationEn;

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
    public $nameIdFormat = \EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_PERSISTENT;

    /**
     * @var string[]
     */
    public $nameIdFormats = array(
        \EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_TRANSIENT,
        \EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_PERSISTENT,
    );

    /**
     * @var IndexedService[]
     */
    public $singleLogoutServices = array();

    /**
     * @var \DateTime
     */
    public $publishInEduGainDate;

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
}