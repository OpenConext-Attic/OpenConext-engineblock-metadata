<?php

namespace OpenConext\Component\EngineBlockMetadata\ServiceRegistry;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\MetadataRepositoryInterface;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class RepositoryAdapter implements AdapterInterface
{
    /**
     * @param array $config
     * @return AdapterInterface
     */
    public static function createFromConfig(array $config)
    {
    }

    public function __construct(MetadataRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Given a list of (SAML2) entities, filter out the idps that are not allowed
     * for the given Service Provider.
     *
     * @param AbstractConfigurationEntity[] $entities
     * @param string $spEntityId
     * @return AbstractConfigurationEntity[] Filtered entities
     */
    public function filterEntitiesBySp(array $entities, $spEntityId)
    {
        return $this->filterIdpsAllowedForSp(
            $spEntityId,
            $entities,
            function(array &$entities, $entityId, IdentityProviderEntity $entity) {
                unset($entities[$entityId]);
            }
        );
    }

    /**
     * Given a list of (SAML2) entities, mark those idps that are not allowed
     * for the given Service Provider.
     *
     * @param AbstractConfigurationEntity[] $entities
     * @param string $spEntityId
     * @return AbstractConfigurationEntity[] the entities
     */
    public function markEntitiesBySp(array $entities, $spEntityId)
    {
        return $this->filterIdpsAllowedForSp(
            $spEntityId,
            $entities,
            function($entities, $entityId, IdentityProviderEntity $entity) {
                $entity->inWayf = false;
            }
        );
    }

    /**
     * @param string $spEntityId
     * @param AbstractConfigurationEntity[] $entities
     * @param callback $onFail
     * @return AbstractConfigurationEntity[]
     * @throws \RuntimeException
     */
    private function filterIdpsAllowedForSp($spEntityId, array $entities, $onFail)
    {
        $spEntity = $this->repository->fetchEntityByEntityId($spEntityId);

        if (!$spEntity || !$spEntity instanceof ServiceProviderEntity) {
            throw new \RuntimeException('SP entity id is not an entity Id for an SP?');
        }

        foreach ($entities as $entityId => $entity) {
            if (!$entity instanceof IdentityProviderEntity) {
                continue;
            }

            if (!in_array($entityId, $spEntity->allowedIdpEntityIds)) {
                $entity->access = true;
            }
            else {
                $onFail($entities, $entityId, $entity);
                unset($entities[$entityId]);
            }
        }

        return $entities;
    }

    /**
     * Given a list of (SAML2) entities, filter out the entities that do not have the requested workflow state
     *
     * @param AbstractConfigurationEntity[] $entities
     * @param string $workflowState
     * @return AbstractConfigurationEntity[] Filtered entities
     */
    public function filterEntitiesByWorkflowState(array $entities, $workflowState)
    {
        foreach ($entities as $entityId => $entity) {
            if (!isset($entity->workflowState)) {
                unset($entities[$entityId]);
                continue;
            }

            if ($entity->workflowState !== $workflowState) {
                unset($entities[$entityId]);
            }
        }
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
        $spEntity = $this->repository->fetchEntityByEntityId($spEntityId);
    }

    /**
     * Get the metadata for all entities.
     *
     * @return array
     */
    public function getRemoteMetaData()
    {
        $this->repository->fetchEntities();
    }

    /**
     * Get the details for a given entity.
     *
     * @param string $entityId
     * @return array
     */
    public function getEntity($entityId)
    {
        return $this->repository->fetchEntityByEntityId($entityId);
    }

    /**
     * Get the Attribute Release Policy for a given Service Provider
     *
     * @param string $spEntityId
     * @return array
     */
    public function getArp($spEntityId)
    {
        $spEntity = $this->repository->fetchEntityByEntityId($spEntityId);
    }
}