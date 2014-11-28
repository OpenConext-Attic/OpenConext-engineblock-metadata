<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Assembler;

use OpenConext\Component\EngineBlockMetadata\Logo;
use OpenConext\Component\EngineBlockMetadata\Organization;
use OpenConext\Component\EngineBlockMetadata\Service;
use OpenConext\Component\EngineBlockMetadata\ShibMdScope;
use OpenConext\Component\EngineBlockMetadata\ContactPerson;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\IndexedService;
use OpenConext\Component\EngineBlockMetadata\X509\X509CertificateFactory;
use OpenConext\Component\EngineBlockMetadata\X509\X509CertificateLazyProxy;
use RuntimeException;

/**
 * Class JanusRestV1Assembler
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Translator
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class JanusRestV1Assembler
{
    /**
     * @param $entityId
     * @param array $metadata
     * @return IdentityProvider|ServiceProvider
     * @throws \RuntimeException
     */
    public function translate($entityId, array $metadata)
    {
        if (isset($metadata['AssertionConsumerService:0:Location'])) {
            $entity = new ServiceProvider($entityId);
            $entity = $this->translateCommonMetadata($metadata, $entity);
            $entity = $this->translateServiceProviderMetadata($metadata, $entity);
            return $entity;
        }

        if (isset($metadata['SingleSignOnService:0:Location'])) {
            $entity = new IdentityProvider($entityId);
            $entity = $this->translateCommonMetadata($metadata, $entity);
            $entity = $this->translateIdentityProviderMetadata($metadata, $entity);
            return $entity;
        }

        // @todo log warning
        return null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @param array $metadata
     * @param AbstractRole $entity
     * @return AbstractRole
     */
    public function translateCommonMetadata(array $metadata, AbstractRole $entity)
    {
        $entity->nameEn                 =        self::ifsetor($metadata, 'Name:en'                 , $entity->nameEn);
        $entity->nameNl                 =        self::ifsetor($metadata, 'Name:nl'                 , $entity->nameNl);
        $entity->descriptionEn          =        self::ifsetor($metadata, 'Description:en'          , $entity->descriptionEn);
        $entity->descriptionNl          =        self::ifsetor($metadata, 'Description:nl'          , $entity->descriptionNl);
        $entity->displayNameEn          =        self::ifsetor($metadata, 'DisplayName:en'          , $entity->displayNameEn);
        $entity->displayNameNl          =        self::ifsetor($metadata, 'DisplayName:nl'          , $entity->displayNameNl);

        $entity->keywordsEn             =        self::ifsetor($metadata, 'keywords:en'             , $entity->keywordsEn);
        $entity->keywordsNl             =        self::ifsetor($metadata, 'keywords:nl'             , $entity->keywordsNl);

        $entity->publishInEdugain       = (bool) self::ifsetor($metadata, 'coin:publish_in_edugain' , $entity->publishInEdugain);
        $publishDate = self::ifsetor($metadata, 'coin:publish_in_edugain_date');
        if ($publishDate) {
            $entity->publishInEduGainDate   = date_create()->setTimestamp(strtotime($publishDate));
        }
        $entity->disableScoping         = (bool) self::ifsetor($metadata, 'coin:disable_scoping'    , $entity->disableScoping);
        $entity->additionalLogging      = (bool) self::ifsetor($metadata, 'coin:additional_logging' , $entity->additionalLogging);

        $entity->requestsMustBeSigned   = (bool) self::ifsetor($metadata, 'redirect.sign'           , $entity->requestsMustBeSigned);
        $entity->nameIdFormat           =        self::ifsetor($metadata, 'NameIDFormat'            , $entity->nameIdFormat);
        $entity->workflowState          =        self::ifsetor($metadata, 'workflowState'           , $entity->workflowState);

        $entity->logo                   = $this->translateLogo($metadata);
        $entity->organizationEn         = $this->translateOrganizationEn($metadata);
        $entity->organizationNl         = $this->translateOrganizationNl($metadata);
        $entity->certificates           = $this->translateCertificates($metadata);
        $entity->singleLogoutService    = $this->translateSloServices($metadata);
        $entity->nameIdFormats          = $this->translateNameIdFormats($metadata, $entity->nameIdFormats);
        $entity->contactPersons         = $this->translateContactPersons($metadata);

        return $entity;
    }

    /**
     * @param array $metadata
     * @param ServiceProvider $entity
     * @return ServiceProvider
     */
    public function translateServiceProviderMetadata(array $metadata, ServiceProvider $entity)
    {
        $entity->isTransparentIssuer        = (bool) self::ifsetor($metadata, 'coin:transparant_issuer'             , $entity->isTransparentIssuer);
        $entity->isTrustedProxy             = (bool) self::ifsetor($metadata, 'coin:trusted_proxy'                  , $entity->isTrustedProxy);
        $entity->implicitVoId               =        self::ifsetor($metadata, 'coin:implicit_vo_id'                 , $entity->implicitVoId);
        $entity->displayUnconnectedIdpsWayf = (bool) self::ifsetor($metadata, 'coin:display_unconnected_idps_wayf'  , $entity->displayUnconnectedIdpsWayf);
        $entity->isConsentRequired          = (bool) self::ifsetor($metadata, 'coin:no_consent_required'            , $entity->isConsentRequired);
        $entity->eula                       =        self::ifsetor($metadata, 'coin:eula'                           , $entity->eula);
        $entity->skipDenormalization        = (bool) self::ifsetor($metadata, 'coin:do_not_add_attribute_aliases'   , $entity->skipDenormalization);

        $entity->assertionConsumerServices = $this->translateIndexedServices($metadata, 'AssertionConsumerService');

        return $entity;
    }

    // @codingStandardsIgnoreEnd

    /**
     * @param array $metadata
     * @param IdentityProvider $entity
     * @return IdentityProvider
     */
    public function translateIdentityProviderMetadata(array $metadata, IdentityProvider $entity)
    {
        $entity->singleSignOnServices   = $this->translateIndexedServices($metadata, 'SingleSignOnService');
        $entity->schacHomeOrganization  = self::ifsetor($metadata, 'coin:schachomeorganization');
        $entity->hidden                 = (bool) self::ifsetor($metadata, 'coin:hidden');

        $guestQualifier = self::ifsetor($metadata, 'coin:guest_qualifier', $entity->guestQualifier);
        if (in_array($guestQualifier, $entity->GUEST_QUALIFIERS)) {
            $entity->guestQualifier = $guestQualifier;
        }

        $entity->shibMdScopes               = $this->translateShibMdScopes($metadata);
        $entity->spsEntityIdsWithoutConsent = $this->translateSpEntityIdsWithoutConsent($metadata);

        return $entity;
    }

    /**
     * @param array $metadata
     * @return array
     */
    private function translateCertificates(array $metadata)
    {
        $certificateFactory = new X509CertificateFactory();
        $certificates = array();

        // Try the primary certificate.
        $certData = self::ifsetor($metadata, 'certData');
        if (!$certData) {
            return $certificates;
        }

        $certificates[] = new X509CertificateLazyProxy($certificateFactory, $certData);

        // If we have a primary we may have a secondary.
        $certData2 = self::ifsetor($metadata, 'certData2');
        if (!$certData2) {
            return $certificates;
        }

        $certificates[] = new X509CertificateLazyProxy($certificateFactory, $certData2);

        // If we have a secondary we may have a tertiary.
        $certData3 = self::ifsetor($metadata, 'certData3');
        if (!$certData3) {
            return $certificates;
        }

        $certificates[] = new X509CertificateLazyProxy($certificateFactory, $certData3);

        return $certificates;
    }

    /**
     * @param array $metadata
     * @param $type
     * @return array
     * @throws \RuntimeException
     */
    private function translateIndexedServices(array $metadata, $type)
    {
        $services = array();
        for ($i = 0; $i < 10; $i++) {
            $bindingKey = $type . ":$i:Binding";
            $bindingValue = self::ifsetor($metadata, $bindingKey);

            $locationKey = $type . ":$i:Location";
            $locationValue = self::ifsetor($metadata, $locationKey);

            if (!$bindingValue && !$locationValue) {
                continue;
            }

            if (!$bindingValue && $locationValue) {
                throw new \RuntimeException("$type Location set '$locationValue' without binding.");
            }

            if ($bindingValue && !$locationValue) {
                throw new \RuntimeException("$type Binding set '$bindingValue' without location.");
            }

            $services[$i] = new IndexedService($locationValue, $bindingValue, $i);
        }
        return $services;
    }

    /**
     * @param array $metadata
     * @return null|Logo
     */
    private function translateLogo(array $metadata)
    {
        $url = self::ifsetor($metadata, 'logo:0:url');
        if (!$url) {
            return null;
        }

        $logo = new Logo($url);
        $logo->width  = self::ifsetor($metadata, 'logo:0:width');
        $logo->height = self::ifsetor($metadata, 'logo:0:height');
        return $logo;
    }

    // @codingStandardsIgnoreStart

    /**
     * @param array $metadata
     * @return null|Organization
     */
    private function translateOrganizationNl(array $metadata)
    {
        $organizationNameNl         = self::ifsetor($metadata, 'OrganizationName:nl'        , '');
        $organizationDisplayNameNl  = self::ifsetor($metadata, 'OrganizationDisplayName:nl' , '');
        $organizationUrlNl          = self::ifsetor($metadata, 'OrganizationURL:nl'         , '');

        if (!$organizationNameNl || !$organizationDisplayNameNl || !$organizationUrlNl) {
            return null;
        }

        return new Organization($organizationDisplayNameNl, $organizationNameNl, $organizationUrlNl);
    }

    /**
     * @param array $metadata
     * @return null|Organization
     */
    private function translateOrganizationEn(array $metadata)
    {
        $organizationNameEn         = self::ifsetor($metadata, 'OrganizationName:en'        , false);
        $organizationDisplayNameEn  = self::ifsetor($metadata, 'OrganizationDisplayName:en' , false);
        $organizationUrlEn          = self::ifsetor($metadata, 'OrganizationURL:en'         , false);

        if (!$organizationNameEn || !$organizationDisplayNameEn || !$organizationUrlEn) {
            return null;
        }

        return new Organization($organizationNameEn, $organizationDisplayNameEn, $organizationUrlEn);
    }

    // @codingStandardsIgnoreEnd

    /**
     * @param array $metadata
     * @param array $defaults
     * @return array
     */
    private function translateNameIdFormats(array $metadata, array $defaults)
    {
        $nameIdFormats = array_filter(array(
            self::ifsetor($metadata, 'NameIDFormats:0'),
            self::ifsetor($metadata, 'NameIDFormats:1'),
            self::ifsetor($metadata, 'NameIDFormats:2'),
        ));
        if (empty($nameIdFormats)) {
            return $defaults;
        }

        return $nameIdFormats;
    }

    /**
     * @param array $metadata
     * @return null|Service
     */
    private function translateSloServices(array $metadata)
    {
        $sloBinding  = self::ifsetor($metadata, 'SingleLogoutService_Binding');
        $sloLocation = self::ifsetor($metadata, 'SingleLogoutService_Location');

        if (!$sloBinding || !$sloLocation) {
            return null;
        }

        return new Service($sloLocation, $sloBinding);
    }

    /**
     * @param array $metadata
     * @return array
     */
    private function translateShibMdScopes(array $metadata)
    {
        $scopes = array();
        for ($i = 0; $i < 10; $i++) {
            $allowedKey = "shibmd:scope:$i:allowed";
            $allowedValue = self::ifsetor($metadata, $allowedKey);

            $regexpKey = "shibmd:scope:$i:regexp";
            $regexpValue = self::ifsetor($metadata, $regexpKey);

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
     * @param array $metadata
     * @return array
     */
    private function translateContactPersons(array $metadata)
    {
        $contactPersons = array();
        for ($i = 0; $i < 3; $i++) {
            $contactTypeKey = "contacts:$i:contactType";
            $contactType = self::ifsetor($metadata, $contactTypeKey);
            if ($contactType) {
                $contactPerson = new ContactPerson($contactType);
                $contactPerson->emailAddress = self::ifsetor($metadata, "contacts:$i:emailAddress", '');
                $contactPerson->givenName    = self::ifsetor($metadata, "contacts:$i:givenName", '');
                $contactPerson->surName      = self::ifsetor($metadata, "contacts:$i:surName", '');
                $contactPersons[] = $contactPerson;
            }
        }
        return $contactPersons;
    }

    /**
     * @param array $metadata
     * @return array
     */
    private function translateSpEntityIdsWithoutConsent(array $metadata)
    {
        $i = 0;
        $spsEntityIdsWithoutConsent = array();
        /** @noinspection PhpAssignmentInConditionInspection */
        while ($disableConsentEntityId = self::ifsetor($metadata, 'disableConsent:' . $i)) {
            $spsEntityIdsWithoutConsent[] = $disableConsentEntityId;
        }

        return $spsEntityIdsWithoutConsent;
    }

    /**
     * @param $entity
     * @param $property
     * @param null $default
     * @return null
     */
    private static function ifsetor($entity, $property, $default = null)
    {
        return isset($entity[$property]) ? $entity[$property] : $default;
    }
}
