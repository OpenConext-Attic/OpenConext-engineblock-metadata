<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\RequestedAttribute;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="service_provider")
 */
class ServiceProvider extends AbstractRole
{
    /**
     * @var IndexedService[]
     */
    protected $assertionConsumerServices = array();

    /**
     * @var bool
     */
    protected $isTransparentIssuer = false;

    /**
     * @var bool
     */
    protected $isTrustedProxy = false;

    /**
     * @var string
     */
    protected $implicitVoId = null;

    /**
     * @var bool
     */
    protected $displayUnconnectedIdpsWayf = false;

    /**
     * @var bool
     */
    protected $isConsentRequired = true;

    /**
     * @var string
     */
    protected $eula = null;

    /**
     * @var bool
     */
    protected $skipDenormalization = false;

    /**
     * @var array
     */
    protected $allowedIdpEntityIds = array();

    /**
     * @var null|RequestedAttribute[]
     */
    protected $requestedAttributes = null;
}
