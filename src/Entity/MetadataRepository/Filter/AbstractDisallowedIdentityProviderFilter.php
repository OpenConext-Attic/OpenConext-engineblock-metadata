<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;

abstract class AbstractDisallowedIdentityProviderFilter extends AbstractFilter
{
    private $serviceProviderEntityId;

    private $allowedIdentityProviderEntityIds;

    /**
     *
     */
    public function __construct($serviceProviderEntityId, $allowedIdentityProviderEntityIds)
    {
        $this->serviceProviderEntityId          = $serviceProviderEntityId;
        $this->allowedIdentityProviderEntityIds = $allowedIdentityProviderEntityIds;
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

        if (in_array($entity->entityId, $this->allowedIdentityProviderEntityIds)) {
            return $entity;
        }

        return $this->onDisallowedIdentityProvider($entity);
    }

    abstract protected function onDisallowedIdentityProvider(IdentityProviderEntity $entity);

    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->serviceProviderEntityId;
    }
}