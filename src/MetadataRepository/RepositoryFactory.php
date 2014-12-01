<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

use OpenConext\Component\EngineBlockMetadata\Container\ContainerInterface;

/**
 * @package OpenConext\Component\EngineBlockMetadata\ServiceRegistry
 */
class RepositoryFactory
{
    /**
     *
     */
    const DEFAULT_NAMESPACE = '\\OpenConext\\Component\\EngineBlockMetadata\\MetadataRepository';

    /**
     * @param array $config
     * @param ContainerInterface $container
     * @return MetadataRepositoryInterface
     * @throws \RuntimeException
     */
    public function createFromConfig(array $config, ContainerInterface $container)
    {
        if (!isset($config['type'])) {
            throw new \RuntimeException('serviceRegistryAdapter config missing type!');
        }

        $namespace = isset($config['namespace']) ? $config['namespace'] : self::DEFAULT_NAMESPACE;
        $className = $namespace . '\\' . $config['type'] . 'MetadataRepository';
        if (!class_exists($className, true)) {
            throw new \RuntimeException("Unable to find '$className'");
        }

        $class = new \ReflectionClass($className);
        if (!$class->implementsInterface(self::DEFAULT_NAMESPACE . '\\MetadataRepositoryInterface')) {
            throw new \RuntimeException("$className does not implement MetadataRepositoryInterface");
        }

        return call_user_func($className . '::createFromConfig', $config, $container);
    }
}
