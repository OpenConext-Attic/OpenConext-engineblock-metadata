<?php

namespace OpenConext\Component\EngineBlockMetadata\Stoker;

/**
 * Load and save the Entity XML.
 * @package OpenConext\Component\EngineBlockMetadata\Stoker
 */
class MetadataEntitySource
{
    /**
     * @var string
     */
    private $metadataDirectory;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->metadataDirectory = $path;
    }

    /**
     * @param string $entityId
     * @return string
     */
    public function load($entityId)
    {
        $filePath = $this->getFilePathForEntityId($entityId);

        if (!file_exists($filePath)) {
            return '';
        }

        return file_get_contents($filePath);
    }

    /**
     * @param string $entityId
     * @param string $entityXml
     * @return bool
     */
    public function save($entityId, $entityXml)
    {
        $filePath = $this->getFilePathForEntityId($entityId);

        if (file_exists($filePath) && md5($entityXml) === md5_file($filePath)) {
            return true;
        }

        return (bool) file_put_contents($filePath, $entityXml);
    }

    /**
     * @param string $entityId
     * @return string
     */
    private function getFilePathForEntityId($entityId)
    {
        $filePath = $this->metadataDirectory . DIRECTORY_SEPARATOR . md5($entityId) . '.xml';
        return $filePath;
    }
}