<?php

namespace OpenConext\Component\EngineBlockMetadata\ServiceRegistry;

use EngineBlock_Application_DiContainer;
use OpenConext\Component\EngineBlockMetadata\Configuration\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;

interface AdapterInterface
{
    /**
     * @param array $config
     * @param EngineBlock_Application_DiContainer $container
     * @return AdapterInterface
     */
    public static function createFromConfig(array $config, EngineBlock_Application_DiContainer $container);

    /**
     * Given a list of (SAML2) entities, filter out the idps that are not allowed
     * for the given Service Provider.
     *
     * @param array $entities
     * @param string $spEntityId
     * @return AbstractConfigurationEntity[] Filtered entities
     */
    public function filterEntitiesBySp(array $entities, $spEntityId);

    /**
     * Given a list of (SAML2) entities, mark those idps that are not allowed
     * for the given Service Provider.
     *
     * @param array $entities
     * @param string $spEntityId
     * @return AbstractConfigurationEntity[] the entities
     */
    public function markEntitiesBySp(array $entities, $spEntityId);

    /**
     * Given a list of (SAML2) entities, filter out the entities that do not have the requested workflow state
     *
     * @param array $entities
     * @param string $workflowState
     * @return AbstractConfigurationEntity[] Filtered entities
     */
    public function filterEntitiesByWorkflowState(array $entities, $workflowState);

    /**
     * Check if a given SP may contact a given Idp
     *
     * @param string $spEntityId
     * @param string $idpEntityId
     * @return bool
     */
    public function isConnectionAllowed($spEntityId, $idpEntityId);

    /**
     * Get the metadata for all entities.
     *
     * @return AbstractConfigurationEntity[]
     */
    public function getRemoteMetaData();

    /**
     * Get the details for a given entity.
     *
     * @param string $entityId
     * @return AbstractConfigurationEntity
     */
    public function getEntity($entityId);

    /**
     * Get the Attribute Release Policy for a given Service Provider
     *
     * @param string $spEntityId
     * @return AttributeReleasePolicy|null
     */
    public function getArp($spEntityId);
}