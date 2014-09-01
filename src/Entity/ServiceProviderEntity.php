<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\IndexedService;

class ServiceProviderEntity extends AbstractConfigurationEntity
{
    /**
     * @var IndexedService[]
     */
    public $assertionConsumerServices;

    /**
     * @var bool
     */
    public $transparentIssuer = false;

    /**
     * @var string
     */
    public $implicitVoId;

    /**
     * @var bool
     */
    public $displayUnconnectedIdpsWayf = false;

    /**
     * @var bool
     */
    public $noConsentRequired = false;

    /**
     * @var string
     */
    public $eula;

    /**
     * @var bool
     */
    public $provideIsMemberOf = false;

    /**
     * @var bool
     */
    public $skipDenormalization = false;

    /**
     * @var array
     */
    public $allowedIdpEntityIds = array();
}