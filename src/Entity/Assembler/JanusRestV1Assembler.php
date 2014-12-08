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
use ReflectionClass;
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
     * @param string $entityId
     * @param array $metadata
     * @return IdentityProvider|ServiceProvider
     * @throws \RuntimeException
     */
    public function assemble($entityId, array $metadata)
    {
        $arguments = array('entityId' => $entityId);

        if (isset($metadata['AssertionConsumerService:0:Location'])) {
            $roleClass = new ReflectionClass('OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider');

            $arguments += $this->assembleAbstractRoleArguments($metadata);
            $arguments += $this->assembleServiceProviderArguments($metadata);

            return $this->instantiate($roleClass, $arguments);
        }

        if (isset($metadata['SingleSignOnService:0:Location'])) {
            $roleClass = new ReflectionClass('OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider');

            $arguments += $this->assembleAbstractRoleArguments($metadata);
            $arguments += $this->assembleIdentityProviderArguments($metadata);

            return $this->instantiate($roleClass, $arguments);
        }

        // @todo log warning
        return null;
    }

    // @codingStandardsIgnoreStart

    /**
     * @param array $metadata
     * @return AbstractRole
     */
    public function assembleAbstractRoleArguments(array $metadata)
    {
        if (isset($metadata['Name:en']))        { $arguments['nameEn'] = $metadata['Name:en']; }
        if (isset($metadata['Name:nl']))        { $arguments['nameNl'] = $metadata['Name:nl']; }
        if (isset($metadata['Description:en'])) { $arguments['descriptionEn'] = $metadata['Description:en']; }
        if (isset($metadata['Description:nl'])) { $arguments['descriptionNl'] = $metadata['Description:nl']; }
        if (isset($metadata['DisplayName:en'])) { $arguments['displayNameEn'] = $metadata['DisplayName:en']; }
        if (isset($metadata['DisplayName:nl'])) { $arguments['displayNameNl'] = $metadata['DisplayName:nl']; }
        if (isset($metadata['keywords:en']))    { $arguments['keywordsEn'] = $metadata['keywords:en']; }
        if (isset($metadata['keywords:nl']))    { $arguments['keywordsNl'] = $metadata['keywords:nl']; }

        if (isset($metadata['coin:publish_in_edugain'])) { $arguments['publishInEdugain'] = (bool) $metadata['coin:publish_in_edugain']; }
        $publishDate = self::ifsetor($metadata, 'coin:publish_in_edugain_date');
        if ($publishDate) {
            $arguments['publishInEduGainDate']   = date_create()->setTimestamp(strtotime($publishDate));
        }
        if (isset($metadata['coin:disable_scoping']))    { $arguments['disableScoping'] = (bool) $metadata['coin:disable_scoping']; }
        if (isset($metadata['coin:additional_logging'])) { $arguments['additionalLogging'] = (bool) $metadata['coin:additional_logging']; }

        if (isset($metadata['redirect.sign'])) { $arguments['requestsMustBeSigned'] = (bool) $metadata['redirect.sign']; }
        if (isset($metadata['NameIDFormat']))  { $arguments['nameIdFormat'] = $metadata['NameIDFormat']; }
        if (isset($metadata['workflowState'])) { $arguments['workflowState'] = $metadata['workflowState']; }

        $arguments['logo']                   = $this->assembleLogo($metadata);
        $arguments['organizationEn']         = $this->assembleOrganizationEn($metadata);
        $arguments['organizationNl']         = $this->assembleOrganizationNl($metadata);
        $arguments['certificates']           = $this->assembleCertificates($metadata);
        $arguments['singleLogoutService']    = $this->assembleSloServices($metadata);

        $supportedNameIdFormats = $this->assembleNameIdFormats($metadata);
        if ($supportedNameIdFormats) $arguments['supportedNameIdFormats'] = $supportedNameIdFormats;
        $arguments['contactPersons']         = $this->assembleContactPersons($metadata);

        return $arguments;
    }

    /**
     * @param array $metadata
     * @param ServiceProvider $entity
     * @return ServiceProvider
     */
    public function assembleServiceProviderArguments(array $metadata)
    {
        if (isset($metadata['coin:transparant_issuer']))            { $arguments['isTransparentIssuer']         = (bool) $metadata['coin:transparant_issuer']; }
        if (isset($metadata['coin:trusted_proxy']))                 { $arguments['isTrustedProxy']              = (bool) $metadata['coin:trusted_proxy']; }
        if (isset($metadata['coin:implicit_vo_id']))                { $arguments['implicitVoId']                = $metadata['coin:implicit_vo_id']; }
        if (isset($metadata['coin:display_unconnected_idps_wayf'])) { $arguments['displayUnconnectedIdpsWayf']  = (bool) $metadata['coin:display_unconnected_idps_wayf']; }
        if (isset($metadata['coin:no_consent_required']))           { $arguments['isConsentRequired']           = (bool) $metadata['coin:no_consent_required']; }
        if (isset($metadata['coin:eula']))                          { $arguments['termsOfServiceUrl']           = $metadata['coin:eula']; }
        if (isset($metadata['coin:do_not_add_attribute_aliases']))  { $arguments['skipDenormalization']         = (bool) $metadata['coin:do_not_add_attribute_aliases']; }

        $arguments['assertionConsumerServices'] = $this->assembleIndexedServices($metadata, 'AssertionConsumerService');

        return $arguments;
    }

    /**
     * @param array $metadata
     * @return IdentityProvider
     */
    public function assembleIdentityProviderArguments(array $metadata)
    {
        $arguments['singleSignOnServices'] = $this->assembleIndexedServices($metadata, 'SingleSignOnService');
        if (isset($metadata['coin:schachomeorganization'])) {
            $arguments['schacHomeOrganization'] = $metadata['coin:schachomeorganization'];
        }
        $arguments['hidden']  = (bool) self::ifsetor($metadata, 'coin:hidden');

        if (isset($metadata['coin:guest_qualifiers'])) {
            if (in_array($metadata['coin:guest_qualifiers'], IdentityProvider::$GUEST_QUALIFIERS)) {
                $metadata['guestQualifier'] = $metadata['coin:guest_qualifiers'];
            }
        }

        $arguments['shibMdScopes'] = $this->assembleShibMdScopes($metadata);
        $arguments['spsEntityIdsWithoutConsent'] = $this->assembleSpEntityIdsWithoutConsent($metadata);

        return $arguments;
    }

    // @codingStandardsIgnoreEnd

    /**
     * @param array $metadata
     * @return array
     */
    private function assembleCertificates(array $metadata)
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
    private function assembleIndexedServices(array $metadata, $type)
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
                // @todo warn
                continue;
            }

            if ($bindingValue && !$locationValue) {
                // @todo warn
                continue;
            }

            $services[$i] = new IndexedService($locationValue, $bindingValue, $i);
        }
        return $services;
    }

    /**
     * @param array $metadata
     * @return null|Logo
     */
    private function assembleLogo(array $metadata)
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
    private function assembleOrganizationNl(array $metadata)
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
    private function assembleOrganizationEn(array $metadata)
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
     * @return array|null
     */
    private function assembleNameIdFormats(array $metadata)
    {
        $nameIdFormats = array_filter(array(
            self::ifsetor($metadata, 'NameIDFormats:0'),
            self::ifsetor($metadata, 'NameIDFormats:1'),
            self::ifsetor($metadata, 'NameIDFormats:2'),
        ));
        if (empty($nameIdFormats)) {
            return null;
        }

        return $nameIdFormats;
    }

    /**
     * @param array $metadata
     * @return null|Service
     */
    private function assembleSloServices(array $metadata)
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
    private function assembleShibMdScopes(array $metadata)
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
    private function assembleContactPersons(array $metadata)
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
    private function assembleSpEntityIdsWithoutConsent(array $metadata)
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

    /**
     * @param ReflectionClass $roleClass
     * @param array $namedArguments
     * @return AbstractRole
     */
    private function instantiate(ReflectionClass $roleClass, array $namedArguments)
    {
        $parameters = $roleClass->getConstructor()->getParameters();

        $positionalDefaultFilledArguments = array();
        foreach ($parameters as $parameter) {
            // Do we have an argument set? If so use that.
            if (isset($namedArguments[$parameter->name])) {
                $positionalDefaultFilledArguments[] = $namedArguments[$parameter->name];
                continue;
            }

            // Otherwise use the default.
            $positionalDefaultFilledArguments[] = $parameter->getDefaultValue();
        }

        return $roleClass->newInstanceArgs($positionalDefaultFilledArguments);
    }
}
