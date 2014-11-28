<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Class RemoveEntityByEntityId
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
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
     * @param AbstractRole $entity
     * @return AbstractRole
     */
    public function filter(AbstractRole $entity)
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
