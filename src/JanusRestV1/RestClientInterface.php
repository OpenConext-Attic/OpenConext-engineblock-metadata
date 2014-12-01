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
     * @return mixed
     */
    public function getAllowedIdps($entityId);

    /**
     * @param $entityId
     * @return mixed
     */
    public function getEntity($entityId);

    /**
     * @return mixed
     */
    public function getIdpList();

    /**
     * @return mixed
     */
    public function getSpList();
}
