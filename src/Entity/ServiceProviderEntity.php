<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Configuration\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Configuration\RequestedAttribute;
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
    public $transparentIssuer = false;

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
    public $noConsentRequired = false;

    /**
     * @var string
     */
    public $eula = null;

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

    /**
     * Attribute Release Policy
     *
     * Note that this may be NULL to indicate that there is NO release policy.
     * Or an empty array, indicating there IS an attribute release policy to release no attributes.
     *
     * @var null|AttributeReleasePolicy
     */
    public $attributeReleasePolicy = null;

    /**
     * @var null|RequestedAttribute[]
     */
    public $requestedAttributes = null;
}