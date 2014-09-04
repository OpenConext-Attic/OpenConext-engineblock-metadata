<?php

namespace OpenConext\Component\EngineBlockMetadata\Translator;

use Janus\ServiceRegistry\Connection\ConnectionDto;
use Janus\ServiceRegistry\Connection\Metadata\MetadataDto;
use Janus\ServiceRegistry\Entity\Connection;
use OpenConext\Component\EngineBlockMetadata\Configuration\AttributeReleasePolicy;
use OpenConext\Component\EngineBlockMetadata\Configuration\Logo;
use OpenConext\Component\EngineBlockMetadata\Configuration\Organization;
use OpenConext\Component\EngineBlockMetadata\Configuration\ShibMdScope;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;
use OpenConext\Component\EngineBlockMetadata\IndexedService;

class JanusTranslator implements EntityTranslatorInterface
{
    public function translate($janusConnection)
    {
        if (!$this->accept($janusConnection)) {
            throw new \RuntimeException('Unable to translate: ' . $janusConnection);
        }

        return $this->doTranslate($janusConnection);
    }

    public function accept($entity)
    {
        return $entity instanceof Connection;
    }

    public function doTranslate(Connection $janusConnection)
    {
        $dto = $janusConnection->createDto();

        $metadataDto = $dto->getMetadata();
        if (!$metadataDto) {
            $entityId = $dto->getName();
            throw new \RuntimeException("Entity '$entityId' does not have metadata in the DTO.");
        }

        if ($dto->getType() === Connection::TYPE_SP) {
            $entity = new ServiceProviderEntity();
            $entity = $this->translateCommon($dto, $entity);
            $entity = $this->translateServiceProvider($dto, $entity);
            $entity = $this->translateCommonMetadata($metadataDto, $entity);
            return $this->translateServiceProviderMetadata($dto, $entity);
        }

        if ($dto->getType() === Connection::TYPE_IDP) {
            $entity = new IdentityProviderEntity();
            $entity = $this->translateCommon($dto, $entity);
            $entity = $this->translateIdentityProvider($dto, $entity);
            $entity = $this->translateCommonMetadata($metadataDto, $entity);
            return $this->translateIdentityProviderMetadata($dto, $entity);
        }

        throw new \RuntimeException('Unable to translate type: "' . $dto->getType() . '"');
    }

    public function translateServiceProviderMetadata(ConnectionDto $dto, ServiceProviderEntity $entity)
    {
        $entity->transparentIssuer          = (bool) $dto->offsetGet('coin:transparant_issuer');
        $entity->implicitVoId               = $dto->offsetGet('coin:implicit_vo_id');
        $entity->displayUnconnectedIdpsWayf = (bool) $dto->offsetGet('coin:display_unconnected_idps_wayf');
        $entity->noConsentRequired          = (bool) $dto->offsetGet('coin:no_consent_required');
        $entity->eula                       = $dto->offsetGet('coin:eula');
        $entity->provideIsMemberOf          = (bool) $dto->offsetGet('coin:provide_is_member_of');
        $entity->skipDenormalization        = (bool) $dto->offsetGet('coin:do_not_add_attribute_aliases');

        $entity->assertionConsumerServices = $this->translateIndexedServices($dto, 'AssertionConsumerService');

        return $entity;
    }

    public function translateIdentityProviderMetadata(MetadataDto $dto, IdentityProviderEntity $entity)
    {
        $entity->singleSignOnServices   = $this->translateIndexedServices($dto, 'SingleSignOnService');
        $entity->schacHomeOrganization  = $dto->offsetGet('coin:schachomeorganization');
        $entity->hidden                 = (bool) $dto->offsetGet('coin:hidden');

        $isValidGuestQualifier = in_array($dto->offsetGet('coin:guest_qualifier'), $entity->GUEST_QUALIFIERS);
        if ($isValidGuestQualifier) {
            $entity->guestQualifier = $dto->offsetGet('coin:guest_qualifier');
        }

        $entity->shibMdScopes       = $this->translateShibMdScopes($dto);

        return $entity;
    }

    public function translateCommonMetadata(MetadataDto $dto, AbstractConfigurationEntity $entity)
    {
        $entity->nameEn                 = $dto->offsetGet('Name:en');
        $entity->nameNl                 = $dto->offsetGet('Name:nl');
        $entity->descriptionEn          = $dto->offsetGet('Description:en');
        $entity->descriptionNl          = $dto->offsetGet('Description:nl');
        $entity->displayNameEn          = $dto->offsetGet('DisplayName:en');
        $entity->displayNameNl          = $dto->offsetGet('DisplayName:nl');

        $entity->keywordsEn             = $dto->offsetGet('keywords:en') ? $dto->offsetGet('keywords:en') : '';
        $entity->keywordsEn             = $dto->offsetGet('keywords:nl') ? $dto->offsetGet('keywords:nl') : '';

        $entity->publishInEdugain       = (bool) $dto->offsetGet('coin:publish_in_edugain');
        if ($publishDate = $dto->offsetGet('coin:publish_in_edugain_date')) {
            $entity->publishInEduGainDate   = date_create()->setTimestamp(strtotime($publishDate));
        }
        $entity->disableScoping         = (bool) $dto->offsetGet('coin:disable_scoping');
        $entity->additionalLogging      = (bool) $dto->offsetGet('coin:additional_logging');

        $entity->requestsMustBeSigned   = (bool) $dto->offsetGet('redirect.sign');
        $entity->nameIdFormat           = $dto->offsetGet('NameIDFormat');

        $entity->logo                   = $this->translateLogo($dto);
        $entity->organizationEn         = $this->translateOrganizationEn($dto);
        $entity->organizationNl         = $this->translateOrganizationNl($dto);
        $entity->certificates           = $this->translateCertificates($dto);
        $entity->singleLogoutServices   = $this->translateSloServices($dto);
        $entity->nameIdFormats          = $this->translateNameIdFormats($dto, $entity->nameIdFormats);
        $entity->contactPersons         = $this->translateContactPersons($dto);

        return $entity;
    }

    private function translateCertificates(MetadataDto $dto)
    {
        $certificateFactory = new \EngineBlock_X509_CertificateFactory();
        $certificates = array();

        // Try the primary certificate.
        $certData = $dto->offsetGet('certData');
        if (!$certData) {
            return $certificates;
        }
        
        $certificates[] = new \EngineBlock_X509_CertificateLazyProxy($certificateFactory, $certData);

        // If we have a primary we may have a secondary.
        $certData2 = $dto->offsetGet('certData2');
        if (!$certData2) {
            return $certificates;
        }
        
        $certificates[] = new \EngineBlock_X509_CertificateLazyProxy($certificateFactory, $certData2);

        // If we have a secondary we may have a tertiary.
        $certData3 = $dto->offsetGet('certData3');
        if (!$certData3) {
            return $certificates;
        }

        $certificates[] = new \EngineBlock_X509_CertificateLazyProxy($certificateFactory, $certData3);

        return $certificates;
    }

    private function translateIndexedServices(MetadataDto $dto, $type)
    {
        $services = array();
        for ($i = 0; $i < 10; $i++) {
            $bindingKey = $type . ":$i:Binding";
            $bindingValue = $dto->offsetGet($bindingKey);

            $locationKey = "AssertionConsumerService:$i:Location";
            $locationValue = $dto->offsetGet($locationKey);

            if (!$bindingValue && !$locationValue) {
                continue;
            }

            if ($bindingValue || $locationValue) {
                throw new \RuntimeException("Binding set without Location!");
            }

            $service = new IndexedService();
            $service->serviceIndex = $i;
            $service->binding = $bindingValue;
            $service->location = $locationValue;

            $services[$i] = $service;
        }
        return $services;
    }

    /**
     * @param MetadataDto $dto
     * @return null|Logo
     */
    private function translateLogo(MetadataDto $dto)
    {
        $logoUrl = $dto->offsetGet('logo:0:url');
        if (!$logoUrl) {
            return null;
        }

        $logo = new Logo();
        $logo->url = $logoUrl;
        $logo->width = $dto->offsetGet('logo:0:width');
        $logo->height = $dto->offsetGet('logo:0:height');
        return $logo;
    }

    /**
     * @param MetadataDto $dto
     * @return null|Organization
     */
    private function translateOrganizationNl(MetadataDto $dto)
    {
        $organizationNameNl         = $dto->offsetGet('OrganizationName:nl');
        $organizationDisplayNameNl  = $dto->offsetGet('OrganizationDisplayName:nl');
        $organizationUrlNl          = $dto->offsetGet('OrganizationURL:nl');

        if (!$organizationNameNl || !$organizationDisplayNameNl || !$organizationUrlNl) {
            return null;
        }

        $organization = new Organization();
        $organization->url          = $organizationUrlNl;
        $organization->displayName  = $organizationDisplayNameNl;
        $organization->name         = $organizationNameNl;
        return $organization;
    }
    
    /**
     * @param MetadataDto $dto
     * @return null|Organization
     */
    private function translateOrganizationEn(MetadataDto $dto)
    {
        $organizationNameEn         = $dto->offsetGet('OrganizationName:en');
        $organizationDisplayNameEn  = $dto->offsetGet('OrganizationDisplayName:en');
        $organizationUrlEn          = $dto->offsetGet('OrganizationURL:en');

        if (!$organizationNameEn || !$organizationDisplayNameEn || !$organizationUrlEn) {
            return null;
        }

        $organization = new Organization();
        $organization->url          = $organizationUrlEn;
        $organization->displayName  = $organizationDisplayNameEn;
        $organization->name         = $organizationNameEn;
        return $organization;
    }

    /**
     * @param MetadataDto $dto
     * @param array $defaults
     * @return array
     */
    private function translateNameIdFormats(MetadataDto $dto, array $defaults)
    {
        $nameIdFormats = array_filter(array(
            $dto->offsetGet('NameIDFormats:0'),
            $dto->offsetGet('NameIDFormats:1'),
            $dto->offsetGet('NameIDFormats:2')
        ));
        if (empty($nameIdFormats)) {
            return $defaults;
        }

        return $nameIdFormats;
    }

    /**
     * @param MetadataDto $dto
     * @return null|IndexedService
     */
    private function translateSloServices(MetadataDto $dto)
    {
        $sloBinding  = $dto->offsetGet('SingleLogoutService_Binding');
        $sloLocation = $dto->offsetGet('SingleLogoutService_Location');

        if (!$sloBinding || !$sloLocation) {
            return null;
        }

        $sloService = new IndexedService();
        $sloService->binding  = $sloBinding;
        $sloService->location = $sloLocation;
        return $sloService;
    }

    /**
     * @param MetadataDto $dto
     * @return array
     */
    private function translateShibMdScopes(MetadataDto $dto)
    {
        $scopes = array();
        for ($i = 0; $i < 10; $i++) {
            $allowedKey = "shibmd:scope:$i:allowed";
            $allowedValue = $dto->offsetGet($allowedKey);

            $regexpKey = "shibmd:scope:$i:regexp";
            $regexpValue = $dto->offsetGet($regexpKey);

            if (!$allowedValue) {
                continue;
            }

            $scope = new ShibMdScope();
            $scope->allowed = $allowedValue;
            $scope->regexp = $regexpValue;
            $scopes[] = $scope;
        }
        return $scopes;
    }

    /**
     * @param MetadataDto $dto
     * @return array
     */
    private function translateContactPersons(MetadataDto $dto)
    {
        $contactPersons = array();
        for ($i = 0; $i < 3; $i++) {
            $contactTypeKey = "contacts:$i:contactType";
            $contactType = $dto->offsetGet($contactTypeKey);
            if ($contactType) {
                $contactPerson = new ContactPerson();
                $contactPerson->contactType = $contactType;
                $contactPerson->emailAddress = $dto->offsetGet("contacts:$i:emailAddress") ? $dto->offsetGet("contacts:$i:emailAddress") : '';
                $contactPerson->givenName = $dto->offsetGet("contacts:$i:givenName") ? $dto->offsetGet("contacts:$i:givenName") : '';
                $contactPerson->surName = $dto->offsetGet("contacts:$i:surName") ? $dto->offsetGet("contacts:$i:surName") : '';
                $contactPersons[] = $contactPerson;
            }
        }
        return $contactPersons;
    }

    private function translateCommon(ConnectionDto $dto, AbstractConfigurationEntity $entity)
    {
        $entity->manipulationCode   = $dto->getManipulationCode();
        $entity->workflowState      = $dto->getState();
        $entity->allowAllEntities   = $dto->getAllowAllEntities();
        $entity->allowedEntityIds   = array_map(
            function(Connection $connection) {
                return $connection->getName();
            },
            $dto->getAllowedConnections()
        );

        return $entity;
    }

    private function translateIdentityProvider(ConnectionDto $dto, IdentityProviderEntity $entity)
    {
        $entity->spsEntityIdsWithoutConsent = array_map(
            function(Connection $connection) {
                return $connection->getName();
            },
            $dto->getDisableConsentConnections()
        );

        return $entity;
    }

    private function translateServiceProvider(ConnectionDto $dto, ServiceProviderEntity $entity)
    {
        $entity->attributeReleasePolicy = $this->translateArp($dto);

        return $entity;
    }

    private function translateArp(ConnectionDto $dto)
    {
        $arpAttributes = $dto->getArpAttributes();

        if (!$arpAttributes) {
            return null;
        }

        return new AttributeReleasePolicy($arpAttributes);
    }
}