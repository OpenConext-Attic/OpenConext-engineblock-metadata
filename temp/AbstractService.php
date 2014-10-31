<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Role;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Class AbstractService
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Role
 */
abstract class AbstractService
{
    /**
     * @var AbstractRole
     */
    protected $role;

    /**
     * @var string
     */
    public $binding;

    /**
     * @var string
     */
    public $location;
}
