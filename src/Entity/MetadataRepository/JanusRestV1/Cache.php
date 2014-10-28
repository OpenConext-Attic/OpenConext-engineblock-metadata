<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\JanusRestV1;

class Cache
{
    /**
     * @var array
     */
    private $serviceProvidersMetadata;

    /**
     * @var array
     */
    private $identityProvidersMetadata;

    /**
     * @param array $identityProvidersMetadata
     * @param array $serviceProvidersMetadata
     */
    public function __construct(array $identityProvidersMetadata, array $serviceProvidersMetadata)
    {
        $this->identityProvidersMetadata = $identityProvidersMetadata;
        $this->serviceProvidersMetadata  = $serviceProvidersMetadata;
    }

    /**
     * @param $entityId
     * @return null
     */
    public function findMetadataByEntityId($entityId)
    {
        $serviceProvider = $this->findServiceProviderMetadataByEntityId($entityId);
        if ($serviceProvider) {
            return $serviceProvider;
        }

        $identityProvider = $this->findIdentityProviderMetadataByEntityId($entityId);
        if ($identityProvider) {
            return $identityProvider;
        }

        return null;
    }

    public function findServiceProviderMetadataByEntityId($entityId)
    {
        if (!isset($this->serviceProvidersMetadata[$entityId])) {
            return null;
        }

        return $this->serviceProvidersMetadata[$entityId];
    }

    public function findIdentityProviderMetadataByEntityId($entityId)
    {
        if (!isset($this->identityProvidersMetadata[$entityId])) {
            return null;
        }

        return $this->identityProvidersMetadata[$entityId];
    }

    public function findIdentityProvidersMetadata()
    {
        return $this->identityProvidersMetadata;
    }
}
