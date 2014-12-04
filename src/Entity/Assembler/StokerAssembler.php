<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Assembler;

use DOMDocument;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
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
 * Class StokerAssembler
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Translator
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class StokerAssembler
{
    /**
     * @param $entityXml
     * @param MetadataIndex\Entity $metadataIndexEntity
     * @return IdentityProvider|ServiceProvider
     * @throws RuntimeException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function assemble($entityXml, MetadataIndex\Entity $metadataIndexEntity)
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
            return $this->assembleIdentityProvider($metadataIndexEntity, $entityDescriptor, $idpDescriptor);
        }
        if ($spDescriptor) {
            return $this->assembleServiceProvider($entityDescriptor, $spDescriptor);
        }
        if ($idpDescriptor) {
            return $this->assembleIdentityProvider($metadataIndexEntity, $entityDescriptor, $idpDescriptor);
        }

        throw new RuntimeException('Boolean logic no longer works, assume running as part of the Heart of Gold.');
    }

    /**
     * @param MetadataIndex\Entity $metadataIndexEntity
     * @param AbstractRole $entity
     * @param SAML2_XML_md_RoleDescriptor $role
     * @return AbstractRole
     */
    private function assembleCommon(
        MetadataIndex\Entity $metadataIndexEntity,
        AbstractRole $entity,
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
     * @return AbstractRole|IdentityProvider
     */
    protected function assembleIdentityProvider(
        MetadataIndex\Entity $metadataIndexEntity,
        SAML2_XML_md_EntityDescriptor $entityDescriptor,
        SAML2_XML_md_IDPSSODescriptor $idpDescriptor
    ) {
        $entity = new IdentityProvider($entityDescriptor->entityID);

        $entity = $this->assembleCommon($metadataIndexEntity, $entity, $idpDescriptor);

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
     * @return ServiceProvider
     */
    protected function assembleServiceProvider(
        SAML2_XML_md_EntityDescriptor $entityDescriptor,
        SAML2_XML_md_SPSSODescriptor $spDescriptor
    ) {
        $entity = new ServiceProvider($entityDescriptor->entityID);

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
