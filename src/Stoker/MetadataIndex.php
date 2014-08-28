<?php

namespace OpenConext\Component\EngineBlockMetadata\Stoker;

class MetadataIndex
{
    const FILENAME = 'metadata.index.json';

    /**
     * @var string
     */
    private $file;

    /**
     * @var \DateTime
     */
    private $processed;

    /**
     * @var string[]
     */
    private $entities = array();

    /**
     * @var \DateTime
     */
    private $cacheUntil;

    /**
     * @var \DateTime
     */
    private $validUntil = null;

    public function __construct($path, \DateTime $cacheUntil, \DateTime $processed, \DateTime $validUntil)
    {
        $this->file = $path . DIRECTORY_SEPARATOR . self::FILENAME;
        $this->cacheUntil = $cacheUntil;
        $this->processed = $processed;
        $this->validUntil = $validUntil;
    }

    public function isCacheExpired($atDateTime = 'now')
    {
        return $this->cacheUntil < new \DateTime($atDateTime);
    }

    public function isValidityExpired($atDateTime = 'now')
    {
        return $this->validUntil !== null && $this->validUntil < new \DateTime($atDateTime);
    }

    public function addEntityId($entityId)
    {
        $this->entities[] = $entityId;
        return $this;
    }

    public function getEntityIds()
    {
        return $this->entities;
    }

    /**
     * @param $path
     * @return null|MetadataIndex
     * @throws \RuntimeException
     */
    public static function load($path)
    {
        $file = $path . static::FILENAME;
        if (!file_exists($file)) {
            return null;
        }

        if (!is_readable($file)) {
            throw new \RuntimeException("File at '$file' exists but is unreadable?");
        }

        $jsonString = file_get_contents($file);
        if (!$jsonString) {
            throw new \RuntimeException("Unable to load metadataIndex file from '$file'");
        }

        $decoded = json_decode($jsonString, true);
        if (!$decoded) {
            throw new \RuntimeException("Unable to decode '$jsonString' as valid JSON.");
        }

        $validUntil = null;
        if (isset($decoded['validUntil']) && $decoded['validUntil']) {
            $validUntil = static::fromJsonDateTime($decoded, 'validUntil');
        }

        return new static(
            $file,
            static::fromJsonDateTime($decoded, 'cacheUntil'),
            static::fromJsonDateTime($decoded, 'processed'),
            $validUntil
        );
    }

    private static function fromJsonDateTime($jsonData, $propertyName)
    {
        if (!isset($jsonData[$propertyName])) {
            throw new \RuntimeException('MetadataIndex is corrupt? Unable to find processed time.');
        }
        $encodedProperty = $jsonData[$propertyName];

        $processed = date_create($encodedProperty);
        if (!$processed) {
            throw new \RuntimeException(
                "MetadataIndex is corrupt? Unable to parse processed time '$encodedProperty' as a date time."
            );
        }
        return $processed;
    }

    public function save()
    {
        file_put_contents(
            $this->file,
            json_encode(array(
                'processed' => $this->processed->format('c'),
                'entities'  => $this->entities,
                'cacheUntil' => $this->cacheUntil->format('c'),
                'validUntil' => $this->validUntil ? $this->validUntil->format('c') : null,
            )));
    }
}