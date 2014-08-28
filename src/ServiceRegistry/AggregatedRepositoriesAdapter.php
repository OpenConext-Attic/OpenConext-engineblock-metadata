<?php

namespace OpenConext\Component\EngineBlockMetadata\ServiceRegistry;

class AggregatedRepositoriesAdapter implements AdapterInterface
{
    public static function create($config)
    {

    }

    /**
     * Given a list of (SAML2) entities, filter out the idps that are not allowed
     * for the given Service Provider.
     *
     * @param array $entities
     * @param string $spEntityId
     * @return array Filtered entities
     */
    public function filterEntitiesBySp(array $entities, $spEntityId)
    {
        // TODO: Implement filterEntitiesBySp() method.
    }

    /**
     * Given a list of (SAML2) entities, mark those idps that are not allowed
     * for the given Service Provider.
     *
     * @param array $entities
     * @param string $spEntityId
     * @return array the entities
     */
    public function markEntitiesBySp(array $entities, $spEntityId)
    {
        // TODO: Implement markEntitiesBySp() method.
    }

    /**
     * Given a list of (SAML2) entities, filter out the entities that do not have the requested workflow state
     *
     * @param array $entities
     * @param string $workflowState
     * @return array Filtered entities
     */
    public function filterEntitiesByWorkflowState(array $entities, $workflowState)
    {
        // TODO: Implement filterEntitiesByWorkflowState() method.
    }

    /**
     * Check if a given SP may contact a given Idp
     *
     * @param string $spEntityId
     * @param string $idpEntityId
     * @return bool
     */
    public function isConnectionAllowed($spEntityId, $idpEntityId)
    {
        // TODO: Implement isConnectionAllowed() method.
    }

    /**
     * Get the metadata for all entities.
     *
     * @return array
     */
    public function getRemoteMetaData()
    {
        // TODO: Implement getRemoteMetaData() method.
    }

    /**
     * Get the details for a given entity.
     *
     * @param string $entityId
     * @return array
     */
    public function getEntity($entityId)
    {
        // TODO: Implement getEntity() method.
    }

    /**
     * Get the Attribute Release Policy for a given Service Provider
     *
     * @param string $spEntityId
     * @return array
     */
    public function getArp($spEntityId)
    {
        // TODO: Implement getArp() method.
    }
}