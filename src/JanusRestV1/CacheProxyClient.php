<?php

namespace OpenConext\Component\EngineBlockMetadata\JanusRestV1;

/**
 * CacheProxyClient, caches in-memory the results it received.
 * @package OpenConext\Component\EngineBlockMetadata\JanusRestV1
 */
class CacheProxyClient implements RestClientInterface
{
    /**
     * @var RestClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $serviceProvidersMetadata;

    /**
     * @var array
     */
    private $identityProvidersMetadata;

    /**
     * @var
     */
    private $allowedIdpsPerSp = array();

    /**
     * @var
     */
    private $entities = array();

    /**
     * @param RestClientInterface $client
     */
    public function __construct(RestClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $entityId
     * @return mixed
     */
    public function getAllowedIdps($entityId)
    {
        if (isset($this->allowedIdpsPerSp[$entityId])) {
            return $this->allowedIdpsPerSp[$entityId];
        }

        $this->allowedIdpsPerSp[$entityId] = $this->client->getAllowedIdps($entityId);

        return $this->allowedIdpsPerSp[$entityId];
    }

    /**
     * @param $entityId
     * @return mixed
     */
    public function getEntity($entityId)
    {
        if (isset($this->entities[$entityId])) {
            return $this->entities[$entityId];
        }

        $this->entities[$entityId] = $this->client->getEntity($entityId);

        return $this->entities[$entityId];
    }

    /**
     * @return mixed
     */
    public function getIdpList()
    {
        if (isset($this->identityProvidersMetadata)) {
            return $this->identityProvidersMetadata;
        }

        $this->identityProvidersMetadata = $this->client->getIdpList();

        return $this->identityProvidersMetadata;
    }

    /**
     * @return mixed
     */
    public function getSpList()
    {
        if (isset($this->serviceProvidersMetadata)) {
            return $this->serviceProvidersMetadata;
        }

        $this->serviceProvidersMetadata = $this->client->getSpList();

        return $this->serviceProvidersMetadata;
    }

    /**
     * @param string $propertyName
     * @param string $propertyValue
     * @return array
     */
    public function findIdentifiersByMetadata($propertyName, $propertyValue)
    {
        return $this->client->findIdentifiersByMetadata($propertyName, $propertyValue);
    }
}
