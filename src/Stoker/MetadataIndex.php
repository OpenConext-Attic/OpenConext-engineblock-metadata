<?php

namespace OpenConext\Component\EngineBlockMetadata\Stoker;

use DateTime;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataIndex\Entity;

class MetadataIndex
{
    const FILENAME = 'metadata.index.json';

    const TYPE_SP  = 'sp';

    const TYPE_IDP = 'idp';

    /**
     * @var string
     */
    private $file;

    /**
     * @var DateTime
     */
    private $processed;

    /**
     * @var Entity[]
     */
    private $entities = array();

    /**
     * @var DateTime
     */
    private $cacheUntil;

    /**
     * @var DateTime
     */
    private $validUntil = null;

    public function __construct(
        $path,
        DateTime $cacheUntil,
        DateTime $processed,
        DateTime $validUntil,
        array $entities = array()
    ) {
        $this->file = $path . DIRECTORY_SEPARATOR . self::FILENAME;
        $this->cacheUntil = $cacheUntil;
        $this->processed = $processed;
        $this->validUntil = $validUntil;
        $this->entities = $entities;
    }

    public function isCacheExpired($atDateTime = 'now')
    {
        return $this->cacheUntil < new DateTime($atDateTime);
    }

    public function isValidityExpired($atDateTime = 'now')
    {
        return $this->validUntil !== null && $this->validUntil < new DateTime($atDateTime);
    }

    public function addEntity(Entity $entity)
    {
        $this->entities[$entity->entityId] = $entity;
        return $this;
    }

    /**
     * @return Entity[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param $entityId
     * @return null|Entity
     */
    public function getEntityByEntityId($entityId)
    {
        if (!isset($this->entities[$entityId])) {
            return null;
        }

        return $this->entities[$entityId];
    }

    /**
     * @param $path
     * @return null|MetadataIndex
     * @throws \RuntimeException
     */
    public static function load($path)
    {
        $file = $path . DIRECTORY_SEPARATOR . static::FILENAME;
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

        $entities = array();
        foreach ($decoded['entities'] as $entity) {
            $entities[$entity['entityId']] = new Entity(
                $entity['entityId'],
                $entity['types'],
                $entity['displayNameEn'],
                $entity['displayNameNl']
            );
        }

        return new static(
            $file,
            static::fromJsonDateTime($decoded, 'cacheUntil'),
            static::fromJsonDateTime($decoded, 'processed'),
            $validUntil,
            $entities
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