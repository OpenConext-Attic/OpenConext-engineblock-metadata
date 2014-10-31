<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;

/**
 * Class RemoveEntityByEntityId
 * @package OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepository\Filter
 */
class RemoveEntityByEntityId extends AbstractFilter
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * @param string $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @return AbstractConfigurationEntity
     */
    public function filter(AbstractConfigurationEntity $entity)
    {
        return $entity->entityId === $this->entityId ? null : $entity;
    }

    /**
     * @return string
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return parent::__toString() . ' -> ' . $this->entityId;
    }
}
