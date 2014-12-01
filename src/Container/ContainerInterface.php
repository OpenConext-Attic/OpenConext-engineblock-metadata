<?php

namespace OpenConext\Component\EngineBlockMetadata\Container;

interface ContainerInterface
{
    public function getServiceRegistryClient();

    public function getEntityManager();
}
