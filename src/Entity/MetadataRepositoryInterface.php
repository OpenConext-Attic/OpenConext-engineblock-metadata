<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

interface MetadataRepositoryInterface
{
    /**
     * @param $entityId
     * @return AbstractConfigurationEntity
     */
    public function fetchEntityByEntityId($entityId);

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function fetchAllEntities();
}