<?php

namespace OpenConext\Component\EngineBlockMetadata\JanusRestV1;

/**
 * Interface RestClientInterface
 * @package OpenConext\Component\EngineBlockMetadata\JanusRestV1
 */
interface RestClientInterface
{
    /**
     * @param $entityId
     * @return string[]
     */
    public function getAllowedIdps($entityId);

    /**
     * @param $entityId
     * @return array
     */
    public function getEntity($entityId);

    /**
     * @return array
     */
    public function getIdpList();

    /**
     * @return array
     */
    public function getSpList();

    /**
     * @param string $propertyName
     * @param string $propertyValue
     * @return array
     */
    public function findIdentifiersByMetadata($propertyName, $propertyValue);
}
