<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository;

/**
 * @package OpenConext\Component\EngineBlockMetadata\ServiceRegistry
 */
class RepositoryFactory
{
    const DEFAULT_NAMESPACE = '\\OpenConext\\Component\\EngineBlockMetadata\\Entity\\MetadataRepository';

    /**
     * @param array $config
     * @param \EngineBlock_Application_DiContainer $container
     * @return MetadataRepositoryInterface
     * @throws \RuntimeException
     */
    public function createFromConfig(array $config, \EngineBlock_Application_DiContainer $container)
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
