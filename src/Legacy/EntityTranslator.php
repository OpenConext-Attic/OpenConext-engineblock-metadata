<?php

namespace OpenConext\Component\EngineBlockMetadata\Legacy;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class EntityTranslator
{
    public function translateServiceProvider(ServiceProviderEntity $entity)
    {
        $cortoEntity = array();
        return $cortoEntity;
    }

    public function translateIdentityProvider(IdentityProviderEntity $entity)
    {
        $cortoEntity = array();
        return $cortoEntity;
    }
}