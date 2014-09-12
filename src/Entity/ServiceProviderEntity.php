<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\RequestedAttribute;
use OpenConext\Component\EngineBlockMetadata\IndexedService;

class ServiceProviderEntity extends AbstractConfigurationEntity
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
     * Attribute Release Policy
     *
     * Note that this may be NULL to indicate that there is NO release policy.
     * Or an empty array, indicating there IS an attribute release policy to release no attributes.
     *
     * @var null|AttributeReleasePolicy
     */
    private $attributeReleasePolicy;

    /**
     * @var null|RequestedAttribute[]
     */
    public $requestedAttributes = null;
}