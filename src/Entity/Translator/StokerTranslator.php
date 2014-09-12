<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Translator;

use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;
use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\Service;
use OpenConext\Component\EngineBlockMetadata\Stoker\MetadataIndex\Entity;

class StokerTranslator
{
    /**
     * @param $entityXml
     * @param Entity $metadataIndexEntity
     * @return IdentityProviderEntity|ServiceProviderEntity
     * @throws \RuntimeException
     */
    public function translate($entityXml, Entity $metadataIndexEntity)
    {
        $document = new \DOMDocument();
        $document->loadXML($entityXml);

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
            $entityXml = new ServiceProviderEntity();

            $singleSignOnServices = array();
            foreach ($spDescriptor->AssertionConsumerService as $acs) {
                $singleSignOnServices[$acs->index] = new IndexedService($acs->Location, $acs->Binding, $acs->index, $acs->isDefault);
            }
            $entityXml->assertionConsumerServices = $singleSignOnServices;

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


            return $entityXml;
        }
        if ($idpDescriptor) {
            $entityXml = new IdentityProviderEntity();

            $singleSignOnServices = array();
            foreach ($idpDescriptor->SingleSignOnService as $ssos) {
                $singleSignOnServices[] = new Service($ssos->Location, $ssos->Binding);
            }
            $entityXml->singleSignOnServices = $singleSignOnServices;
        }

        $entityXml->entityId = $entityDescriptor->entityID;
        $entityXml->displayNameNl = $metadataIndexEntity->displayNameNl;
        $entityXml->displayNameEn = $metadataIndexEntity->displayNameEn;

        return $entityXml;
    }
}