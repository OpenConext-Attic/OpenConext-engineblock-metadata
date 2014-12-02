<?php

namespace OpenConext\Component\EngineBlockMetadata\JanusRestV1;

/**
 * RestClientDecorator adds methods to retrieve metadata for a single entity / idp or sp.
 *
 * Warning: Using this without some kind of cache will lead to horrible performance.
 *
 * @package OpenConext\Component\EngineBlockMetadata\JanusRestV1
 */
class RestClientDecorator implements RestClientInterface
{
    /**
     * @var RestClientInterface
     */
    private $client;

    /**
     * @param RestClientInterface $client
     */
    public function __construct(RestClientInterface $client)
    {
        $this->client = $client;
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

    /**
     * @param $entityId
     * @return null
     */
    public function findServiceProviderMetadataByEntityId($entityId)
    {
        $serviceProvidersMetadata = $this->client->getSpList();

        if (!isset($serviceProvidersMetadata[$entityId])) {
            return null;
        }

        return $serviceProvidersMetadata[$entityId];
    }

    /**
     * @param $entityId
     * @return null
     */
    public function findIdentityProviderMetadataByEntityId($entityId)
    {
        $identityProvidersMetadata = $this->client->getIdpList();

        if (!isset($identityProvidersMetadata[$entityId])) {
            return null;
        }

        return $identityProvidersMetadata[$entityId];
    }

    /**
     * @param $entityId
     * @return string[]
     */
    public function getAllowedIdps($entityId)
    {
        return $this->getAllowedIdps($entityId);
    }

    /**
     * @param $entityId
     * @return array
     */
    public function getEntity($entityId)
    {
        return $this->getEntity($entityId);
    }

    /**
     * @return array
     */
    public function getIdpList()
    {
        return $this->getIdpList();
    }

    /**
     * @return array
     */
    public function getSpList()
    {
        return $this->getSpList();
    }

    /**
     * @param string $propertyName
     * @param string $propertyValue
     * @return array
     */
    public function findIdentifiersByMetadata($propertyName, $propertyValue)
    {
        return $this->findIdentifiersByMetadata($propertyName, $propertyValue);
    }
}
