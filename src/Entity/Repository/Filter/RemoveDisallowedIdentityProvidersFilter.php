<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Repository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class RemoveDisallowedIdentityProvidersFilter implements FilterInterface
{
    /**
     * @var ServiceProviderEntity
     */
    private $serviceProviderEntity;

    /**
     *
     */
    public function __construct(ServiceProviderEntity $serviceProviderEntity)
    {
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

        if ($this->serviceProviderEntity->allowAllEntities) {
            return $entity;
        }

        if (in_array($entity->entityId, $this->serviceProviderEntity->allowedEntityIds)) {
            return $entity;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getServiceProviderEntity()
    {
        return $this->serviceProviderEntity;
    }
}