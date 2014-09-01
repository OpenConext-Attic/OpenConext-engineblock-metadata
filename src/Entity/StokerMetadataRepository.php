<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity;

use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataEntitySource;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataIndex;
use OpenConext\Component\EngineBlockMetadata\Translator\StokerTranslator;

class StokerMetadataRepository implements MetadataRepositoryInterface
{
    /**
     * @var StokerTranslator
     */
    private $translator;

    /**
     * @var MetadataEntitySource
     */
    private $metadataEntitySource;

    /**
     * @var MetadataIndex
     */
    private $metadataIndex;

    /**
     * @param string $metadataDirectory
     * @param StokerTranslator $translator
     */
    public function __construct($metadataDirectory, StokerTranslator $translator)
    {
        $this->metadataEntitySource = new MetadataEntitySource($metadataDirectory);
        $this->metadataIndex = MetadataIndex::load($metadataDirectory);
        $this->metadataDirectory = $metadataDirectory;
        $this->translator = $translator;
    }

    /**
     * @param string $entityId
     * @return AbstractConfigurationEntity
     */
    public function fetchEntityByEntityId($entityId)
    {
        $this->translator->translate($this->metadataEntitySource->load($entityId));
    }

    /**
     * @return AbstractConfigurationEntity[]
     */
    public function fetchAllEntities()
    {
        $entityIds = $this->metadataIndex->getEntityIds();

        $entities = array();
        foreach ($entityIds as $entityId) {
            $entities[] = $this->translator->translate(
                $this->metadataEntitySource->load($entityId)
            );
        }
        return $entities;
    }
}