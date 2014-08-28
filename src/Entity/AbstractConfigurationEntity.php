<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\ContactPerson;

abstract class AbstractConfigurationEntity
{
    const WORKFLOW_STATE_DEFAULT = 'production';

    public $entityId;
    public $nameNl;
    public $nameEn;
    public $descriptionNl;
    public $descriptionEn;
    public $displayNameNl;
    public $displayNameEn;
    public $logo;

    public $organizationNameNl;
    public $organizationNameEn;
    public $organizationDisplayNameNl;
    public $organizationDisplayNameEn;
    public $organizationUrlNl;
    public $organizationUrlEn;
    public $keywordsNl;
    public $keywordsEn;

    public $publishInEdugain = false;
    public $certificates = array();

    public $workflowState = self::WORKFLOW_STATE_DEFAULT;

    /** @var ContactPerson[] */
    public $contactPersons = array();

    public $nameIdFormat;
    public $nameIdFormats = array(
        EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_TRANSIENT,
        EngineBlock_Urn::SAML2_0_NAMEID_FORMAT_PERSISTENT,
    );

    public $singleLogoutService;

    /**
     * @var \DateTime
     */
    public $publishInEduGainDate;
    public $disableScoping = false;
    public $additionalLogging = false;
}