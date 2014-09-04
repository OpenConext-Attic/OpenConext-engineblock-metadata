<?php

namespace OpenConext\Component\EngineBlockMetadata\Legacy;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class EntityTranslator
{
    public function translateServiceProvider(ServiceProviderEntity $entity)
    {
        $cortoEntity = array();

        $this->translateCommon($entity, $cortoEntity);

        // @todo

        return $cortoEntity;
    }

    public function translateIdentityProvider(IdentityProviderEntity $entity)
    {
        $cortoEntity = array();

        $this->translateCommon($entity, $cortoEntity);

        // @todo

        return $cortoEntity;
    }

    private function translateCommon(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
    }
}