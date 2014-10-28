<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Translator;

use DOMDocument;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;
use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\Service;
use OpenConext\Component\StokerMetadata\MetadataIndex;
use RuntimeException;
use SAML2_Const;
use SAML2_XML_md_EntityDescriptor;
use SAML2_XML_md_IDPSSODescriptor;
use SAML2_XML_md_RoleDescriptor;
use SAML2_XML_md_SPSSODescriptor;
use SAML2_XML_mdui_Logo;
use SAML2_XML_mdui_UIInfo;

/**
 * Class StokerTranslator
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Translator
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class StokerTranslator
{
    /**
     * @param $entityXml
     * @param MetadataIndex\Entity $metadataIndexEntity
     * @return IdentityProviderEntity|ServiceProviderEntity
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function translate($entityXml, MetadataIndex\Entity $metadataIndexEntity)
    {
        $document = new DOMDocument();
        $document->loadXML($entityXml);

        $entityDescriptor = new SAML2_XML_md_EntityDescriptor($document->documentElement);

        $idpDescriptor = null;
        $spDescriptor = null;
        foreach ($entityDescriptor->RoleDescriptor as $role) {
            if ($role instanceof SAML2_XML_md_IDPSSODescriptor) {
                if ($idpDescriptor) {
                    throw new RuntimeException('More than 1 IDPSSODescriptor found');
                }
                $idpDescriptor = $role;
            }
            if ($role instanceof SAML2_XML_md_SPSSODescriptor) {
                if ($spDescriptor) {
                    throw new RuntimeException('More than 1 SPSSODescriptor found');
                }
                $spDescriptor = $role;
            }
        }

        if (!$idpDescriptor && !$spDescriptor) {
            throw new RuntimeException('Entity is neither IDP nor SP?');
        }
        if ($spDescriptor && $idpDescriptor) {
            // @todo warn: adding only the idp side!
            return $this->translateIdentityProvider($metadataIndexEntity, $entityDescriptor, $idpDescriptor);
        }
        if ($spDescriptor) {
            return $this->translateServiceProvider($entityDescriptor, $spDescriptor);
        }
        if ($idpDescriptor) {
            return $this->translateIdentityProvider($metadataIndexEntity, $entityDescriptor, $idpDescriptor);
        }

        throw new RuntimeException('Boolean logic no longer works, assume running as part of the Heart of Gold.');
    }

    /**
     * @param MetadataIndex\Entity $metadataIndexEntity
     * @param AbstractConfigurationEntity $entity
     * @param SAML2_XML_md_RoleDescriptor $role
     * @return AbstractConfigurationEntity
     */
    private function translateCommon(
        MetadataIndex\Entity $metadataIndexEntity,
        AbstractConfigurationEntity $entity,
        SAML2_XML_md_RoleDescriptor $role
    ) {
        $entity->displayNameNl = $metadataIndexEntity->displayNameNl;
        $entity->displayNameEn = $metadataIndexEntity->displayNameEn;

        foreach ($role->Extensions as $extension) {
            if (!$extension instanceof SAML2_XML_mdui_UIInfo) {
                continue;
            }

            if (empty($extension->Logo)) {
                continue;
            }

            /** @var SAML2_XML_mdui_Logo $logo */
            $logo = $extension->Logo[0];
            $entity->logo = new Logo($logo->url);
            $entity->logo->height = $logo->height;
            $entity->logo->width  = $logo->width;
        }

        return $entity;
    }

    /**
     * @param MetadataIndex\Entity $metadataIndexEntity
     * @param SAML2_XML_md_EntityDescriptor $entityDescriptor
     * @param SAML2_XML_md_IDPSSODescriptor $idpDescriptor
     * @return AbstractConfigurationEntity|IdentityProviderEntity
     */
    protected function translateIdentityProvider(
        MetadataIndex\Entity $metadataIndexEntity,
        SAML2_XML_md_EntityDescriptor $entityDescriptor,
        SAML2_XML_md_IDPSSODescriptor $idpDescriptor
    ) {
        $entity = new IdentityProviderEntity($entityDescriptor->entityID);

        $entity = $this->translateCommon($metadataIndexEntity, $entity, $idpDescriptor);

        $singleSignOnServices = array();
        foreach ($idpDescriptor->SingleSignOnService as $ssos) {
            if (!in_array($ssos->Binding, array(SAML2_Const::BINDING_HTTP_POST, SAML2_Const::BINDING_HTTP_REDIRECT))) {
                continue;
            }

            $singleSignOnServices[] = new Service($ssos->Location, $ssos->Binding);
        }
        $entity->singleSignOnServices = $singleSignOnServices;
        return $entity;
    }

    /**
     * @param SAML2_XML_md_EntityDescriptor $entityDescriptor
     * @param SAML2_XML_md_SPSSODescriptor $spDescriptor
     * @return ServiceProviderEntity
     */
    protected function translateServiceProvider(
        SAML2_XML_md_EntityDescriptor $entityDescriptor,
        SAML2_XML_md_SPSSODescriptor $spDescriptor
    ) {
        $entity = new ServiceProviderEntity($entityDescriptor->entityID);

        $singleSignOnServices = array();
        foreach ($spDescriptor->AssertionConsumerService as $acs) {
            if (!in_array($acs->Binding, array(SAML2_Const::BINDING_HTTP_POST, SAML2_Const::BINDING_HTTP_REDIRECT))) {
                continue;
            }

            $singleSignOnServices[$acs->index] = new IndexedService(
                $acs->Location,
                $acs->Binding,
                $acs->index,
                $acs->isDefault
            );
        }
        $entity->assertionConsumerServices = $singleSignOnServices;
        return $entity;
    }
}
