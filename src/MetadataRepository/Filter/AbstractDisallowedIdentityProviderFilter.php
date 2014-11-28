<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;

/**
 * Class AbstractDisallowedIdentityProviderFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
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
     * @param AbstractRole $entity
     * @return AbstractRole
     */
    public function filter(AbstractRole $entity)
    {
        if (!$entity instanceof IdentityProvider) {
            return $entity;
        }

        if (in_array($entity->entityId, $this->allowedIdentityProviderEntityIds)) {
            return $entity;
        }

        return $this->onDisallowedIdentityProvider($entity);
    }

    /**
     * @param IdentityProvider $entity
     * @return mixed
     */
    abstract protected function onDisallowedIdentityProvider(IdentityProvider $entity);

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->serviceProviderEntityId;
    }
}
