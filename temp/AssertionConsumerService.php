<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Role;

/**
 * @Entity @Table(name="role_assertion_consumer_service")
 */
class AssertionConsumerService
{
    protected $serviceProvider;

    /**
     * @var string
     */
    protected $entityId;

    /**
     * @var int
     */
    public $serviceIndex;

    /**
     * Note that NULL and FALSE are NOT the same in this context.
     *
     * @var bool|null
     */
    public $isDefault = null;

    /**
     * @var string
     */
    public $binding;

    /**
     * @var string
     */
    public $location;
}
