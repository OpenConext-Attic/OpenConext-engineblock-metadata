<?php

namespace OpenConext\Component\EngineBlockMetadata\Translator;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;
use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataIndex\Entity;

class StokerTranslator implements EntityTranslatorInterface
{
    /**
     * @var Entity
     */
    private $indexedEntity;

    public function setIndexedEntity(Entity $entity)
    {
        $this->indexedEntity = $entity;
    }

    /**
     * @param string $entity
     * @return IdentityProviderEntity|ServiceProviderEntity
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function translate($entity)
    {
        if (!$this->accept($entity)) {
            throw new \InvalidArgumentException('Not a string: '. $entity);
        }

        $document = new \DOMDocument();
        $document->loadXML($entity);

        $entityDescriptor = new \SAML2_XML_md_EntityDescriptor($document->documentElement);

        $idpDescriptor = null;
        $spDescriptor = null;
        foreach ($entityDescriptor->RoleDescriptor as $role) {
            if ($role instanceof \SAML2_XML_md_IDPSSODescriptor) {
                if ($idpDescriptor) {
                    throw new \RuntimeException('More than 1 IDPSSODescriptor found');
                }
                $idpDescriptor = $role;
            }
            if ($role instanceof \SAML2_XML_md_SPSSODescriptor) {
                if ($spDescriptor) {
                    throw new \RuntimeException('More than 1 SPSSODescriptor found');
                }
                $spDescriptor = $role;
            }
        }

        if (!$idpDescriptor && !$spDescriptor) {
            throw new \RuntimeException('Entity is neither IDP nor SP?');
        }
        if ($spDescriptor && $idpDescriptor) {
            throw new \RuntimeException('Entity is both SP and IdP, we do not support this');
        }
        if ($spDescriptor) {
            $entity = new ServiceProviderEntity();

            $singleSignOnServices = array();
            foreach ($spDescriptor->AssertionConsumerService as $acs) {
                $service = new IndexedService();
                $service->serviceIndex  = $acs->index;
                $service->isDefault     = $acs->isDefault;
                $service->binding       = $acs->Binding;
                $service->location      = $acs->Location;
                $singleSignOnServices[$acs->index] = $service;
            }
            $entity->assertionConsumerServices = $singleSignOnServices;

//            /** @var \SAML2_XML_mdui_UIInfo[] $mdUiInfoExtensions */
//            $mdUiInfoExtensions = array_filter(
//                $spDescriptor->Extensions,
//                function($extension) {
//                    return $extension instanceof \SAML2_XML_mdui_UIInfo;
//            });
//
//            if (count($mdUiInfoExtensions) > 1) {
//                throw new \RuntimeException('Entity has more than one MDUIInfo?');
//            }
//            else if (count($mdUiInfoExtensions) === 0) {
//                throw new \RuntimeException('No obligatory MDUiInfo extension!');
//            }
//            $mdUiInfoExtension = $mdUiInfoExtensions[0];


            return $entity;
        }
        if ($idpDescriptor) {
            $entity = new IdentityProviderEntity();

            $singleSignOnServices = array();
            foreach ($idpDescriptor->SingleSignOnService as $ssos) {
                $service = new IndexedService();
                $service->binding   = $ssos->Binding;
                $service->location  = $ssos->Location;
                $singleSignOnServices[] = $service;
            }
            $entity->singleSignOnServices = $singleSignOnServices;
        }

        $entity->entityId = $entityDescriptor->entityID;
        $entity->displayNameNl = $this->indexedEntity->displayNameNl;
        $entity->displayNameEn = $this->indexedEntity->displayNameEn;

        return $entity;
    }

    public function accept($entity)
    {
        return is_string($entity);
    }
}