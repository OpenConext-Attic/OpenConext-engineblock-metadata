<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;

/**
 * Class AbstractDisallowedIdentityProviderFilter
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
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

    /**
     * @param IdentityProviderEntity $entity
     * @return mixed
     */
    abstract protected function onDisallowedIdentityProvider(IdentityProviderEntity $entity);

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->serviceProviderEntityId;
    }
}
