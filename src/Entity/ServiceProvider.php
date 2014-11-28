<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\RequestedAttribute;
use OpenConext\Component\EngineBlockMetadata\IndexedService;

/**
 * Class ServiceProvider
 * @package OpenConext\Component\EngineBlockMetadata\Entity
 */
class ServiceProvider extends AbstractConfigurationEntity
{
    /**
     * @var IndexedService[]
     */
    public $assertionConsumerServices = array();

    /**
     * @var bool
     */
    public $isTransparentIssuer = false;

    /**
     * @var bool
     */
    public $isTrustedProxy = false;

    /**
     * @var string
     */
    public $implicitVoId = null;

    /**
     * @var bool
     */
    public $displayUnconnectedIdpsWayf = false;

    /**
     * @var bool
     */
    public $isConsentRequired = true;

    /**
     * @var string
     */
    public $eula = null;

    /**
     * @var bool
     */
    public $skipDenormalization = false;

    /**
     * @var array
     */
    public $allowedIdpEntityIds = array();

    /**
     * @var null|RequestedAttribute[]
     */
    public $requestedAttributes = null;
}
