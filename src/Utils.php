<?php

namespace OpenConext\Component\EngineBlockMetadata;

use ReflectionClass;

/**
 * The dreaded 'Utils' class with static helper methods.
 * @package OpenConext\Component\EngineBlockMetadata
 */
class Utils
{
    /**
     * Returns the value if it exists or a given default value.
     *
     * @see https://wiki.php.net/rfc/ifsetor
     *
     * @param array $value
     * @param string $property
     * @param mixed $default
     * @return mixed
     */
    public static function ifsetor(array $value, $property, $default = null)
    {
        return isset($value[$property]) ? $value[$property] : $default;
    }

    /**
     * Instantiate a class with named arguments.
     *
     * An answer to a problem that should not exist (legacy large Entities).
     *
     * @param string $className
     * @param array $namedArguments
     * @return object
     */
    public static function instantiate($className, array $namedArguments)
    {
        $reflectionClass = new ReflectionClass($className);
        $parameters = $reflectionClass->getConstructor()->getParameters();

        $positionalDefaultFilledArguments = array();
        foreach ($parameters as $parameter) {
            // Do we have an argument set? If so use that.
            if (isset($namedArguments[$parameter->name])) {
                $positionalDefaultFilledArguments[] = $namedArguments[$parameter->name];
                continue;
            }

            // Otherwise use the default.
            $positionalDefaultFilledArguments[] = $parameter->getDefaultValue();
        }

        return $reflectionClass->newInstanceArgs($positionalDefaultFilledArguments);
    }
}
