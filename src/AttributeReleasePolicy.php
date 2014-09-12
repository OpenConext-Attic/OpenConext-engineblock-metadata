<?php

namespace OpenConext\Component\EngineBlockMetadata;

class AttributeReleasePolicy
{
    /**
     * @var array
     */
    private $attributesWithValues;

    public function __construct(array $attributesWithValues)
    {
        $this->attributesWithValues = $attributesWithValues;
    }

    public function getAttributeNames()
    {
        return array_keys($this->attributesWithValues);
    }

    public function hasAttribute($attributeName)
    {
        return isset($this->attributesWithValues[$attributeName]);
    }

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

            // We support wildcard matching at the end only, like 'some*' would match 'someValue' or 'somethingElse'
            if (substr($allowedValue, -1) !== '*') {
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