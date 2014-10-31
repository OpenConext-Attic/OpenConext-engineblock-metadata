<?php

namespace OpenConext\Component\EngineBlockMetadata;

/**
 * Class AttributeReleasePolicy
 * @package OpenConext\Component\EngineBlockMetadata
 */
class AttributeReleasePolicy
{
    const WILDCARD_CHARACTER = '*';

    /**
     * @var array
     */
    private $attributesWithValues;

    /**
     * @param array $attributesWithValues
     */
    public function __construct(array $attributesWithValues)
    {
        $this->attributesWithValues = $attributesWithValues;
    }

    /**
     * @return array
     */
    public function getAttributeNames()
    {
        return array_keys($this->attributesWithValues);
    }

    /**
     * @param $attributeName
     * @return bool
     */
    public function hasAttribute($attributeName)
    {
        return isset($this->attributesWithValues[$attributeName]);
    }

    /**
     * @param $attributeName
     * @param $attributeValue
     * @return bool
     */
    public function isAllowed($attributeName, $attributeValue)
    {
        if (!$this->hasAttribute($attributeName)) {
            return false;
        }

        foreach ($this->attributesWithValues[$attributeName] as $allowedValue) {
            if ($attributeValue === $allowedValue) {
                // Literal match.
                return true;
            }

            if ($allowedValue === self::WILDCARD_CHARACTER) {
                // Only a single wildcard character, all values are permitted.
                return true;
            }

            // We support wildcard matching at the end only, like 'some*' would match 'someValue' or 'somethingElse'
            if (substr($allowedValue, -1) !== self::WILDCARD_CHARACTER) {
                // Not a supported pattern
                continue;
            }

            // Would contain 'some'
            $patternStart = substr($allowedValue, 0, -1);

            // Does $attributeValue start with 'some'?
            if (strpos($attributeValue, $patternStart) === 0) {
                return true;
            }
        }
        return false;
    }
}
