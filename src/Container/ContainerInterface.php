<?php

namespace OpenConext\Component\EngineBlockMetadata\Container;

use Doctrine\ORM\EntityManager;
use OpenConext\Component\EngineBlockMetadata\JanusRestV1\RestClientInterface;

/**
 * Interface ContainerInterface
 * @package OpenConext\Component\EngineBlockMetadata\Container
 */
interface ContainerInterface
{
    /**
     * @return RestClientInterface
     */
    public function getServiceRegistryClient();

    /**
     * @return EntityManager
     */
    public function getEntityManager();
}
