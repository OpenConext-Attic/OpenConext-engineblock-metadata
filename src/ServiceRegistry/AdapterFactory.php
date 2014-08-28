<?php

namespace OpenConext\Component\EngineBlockMetadata\ServiceRegistry;

/**
 *
 *  ; Read from multiple repositories
    serviceRegistryAdapter.type = AggregatedRepositories
    serviceRegistryAdapter.repository.janus.type = Janus
    serviceRegistryAdapter.repository.janus.dsn = "mysql://engineblock:password@localhost:3306/janus"
    serviceRegistryAdapter.repository.stoker.type = Stoker
    serviceRegistryAdapter.repository.stoker.source.path = /var/cache/openconext/stoker/edugain
    ; Note the ordering:
    serviceRegistryAdapter.repositories[] = database
    serviceRegistryAdapter.repositories[] = stoker
 *
 *
 * @package OpenConext\Component\EngineBlockMetadata\ServiceRegistry
 */
class AdapterFactory
{
    const DEFAULT_NAMESPACE = '\\OpenConext\\Component\\EngineBlockMetadata\ServiceRegistry';

    /**
     *
     *
     *
     * @param array $config
     * @return AdapterInterface
     */
    public function createFromConfig(array $config)
    {
        if (!isset($config['type'])) {
            throw new \RuntimeException('serviceRegistryAdapter config missing type!');
        }

        $namespace = isset($config['namespace']) ? $config['namespace'] : self::DEFAULT_NAMESPACE;
        $className = $namespace . '\\' . $config['type'] . 'Adapter';
        if (!class_exists($className, true)) {
            throw new \RuntimeException("Unable to find '$className'");
        }

        $class = new \ReflectionClass($className);
        if (!$class->implementsInterface(self::DEFAULT_NAMESPACE . '\\AdapterInterface')) {
            throw new \RuntimeException("$className does not implement AdapterInterface");
        }

        return call_user_func($className . '::createFromConfig', $config);
    }
}