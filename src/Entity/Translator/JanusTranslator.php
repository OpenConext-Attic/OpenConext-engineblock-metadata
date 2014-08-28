<?php

namespace OpenConext\Component\EngineBlockMetadata\Translator;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

class JanusTranslator implements EntityTranslatorInterface
{
    public function accept($entity)
    {
        return $entity instanceof \stdClass;
    }

    public function translate($janusConnection)
    {
        if ($janusConnection->isAServiceProvider()) {
            return $janusConnection->translate(new ServiceProviderEntity());
        }

        if ($janusConnection->isAnIdentityProvider()) {
            return $janusConnection->translate(new IdentityProviderEntity());
        }

        throw new \RuntimeException('Unknown Janus connection type');
    }
}