<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\MetadataRepositoryInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class DisableDisallowedEntitiesInWayfFilter extends AbstractFilter
{
    /**
     * @var MetadataRepositoryInterface
     */
    private $repository;

    /**
     * @var ServiceProviderEntity
     */
    private $serviceProviderEntity;

    /**
     *
     */
    public function __construct(MetadataRepositoryInterface $repository, ServiceProviderEntity $serviceProviderEntity)
    {
        $this->repository = $repository;
        $this->serviceProviderEntity = $serviceProviderEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @return AbstractConfigurationEntity
     */
    public function filter(AbstractConfigurationEntity $entity)
    {
        if (!$entity instanceof IdentityProviderEntity) {
            return $entity;
        }

        if ($this->repository->isConnectionAllowed($this->serviceProviderEntity, $entity)) {
            return $entity;
        }

        $entity->enabledInWayf = false;

        return $entity;
    }

    public function setRepository(MetadataRepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return ServiceProviderEntity
     */
    public function getServiceProviderEntity()
    {
        return $this->serviceProviderEntity;
    }
}