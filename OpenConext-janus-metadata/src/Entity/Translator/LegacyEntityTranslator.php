<?php

namespace OpenConext\Component\EngineBlockMetadata\Legacy;

use DateTime;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractConfigurationEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProviderEntity;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProviderEntity;

/**
 * Class EntityTranslator
 * @package OpenConext\Component\EngineBlockMetadata\Legacy
 * @SuppressWarnings(PMD.TooManyMethods)
 */
class EntityTranslator
{
    /**
     * @param ServiceProviderEntity $entity
     * @return array
     */
    public function translateServiceProvider(ServiceProviderEntity $entity)
    {
        $cortoEntity = array();

        $cortoEntity = $this->translateCommon($entity, $cortoEntity);

        if ($entity->isTransparentIssuer) {
            $cortoEntity['TransparentIssuer'] = 'yes';
        }
        if ($entity->implicitVoId) {
            $cortoEntity['VoContext'] = $entity->implicitVoId;
        }
        if ($entity->displayUnconnectedIdpsWayf) {
            $cortoEntity['DisplayUnconnectedIdpsWayf'] = 'yes';
        }
        foreach ($entity->assertionConsumerServices as $service) {
            if (!isset($cortoEntity['AssertionConsumerServices'])) {
                $cortoEntity['AssertionConsumerServices'] = array();
            }

            $cortoEntity[$service->serviceIndex] = array(
                'Binding'  => $service->binding,
                'Location' => $service->location,
            );
        }
        if (!$entity->isConsentRequired) {
            $cortoEntity['NoConsentRequired'] = true;
        }
        if ($entity->skipDenormalization) {
            $cortoEntity['SkipDenormalization'] = true;
        }

        return $cortoEntity;
    }

    /**
     * @param IdentityProviderEntity $entity
     * @return array
     */
    public function translateIdentityProvider(IdentityProviderEntity $entity)
    {
        $cortoEntity = array();

        $this->translateCommon($entity, $cortoEntity);

        foreach ($entity->singleSignOnServices as $service) {
            if (!isset($cortoEntity['SingleSignOnService'])) {
                $cortoEntity['SingleSignOnService'] = array();
            }

            $cortoEntity[] = array(
                'Binding'  => $service->binding,
                'Location' => $service->location,
            );
        }

        $cortoEntity['GuestQualifier'] = $entity->guestQualifier;

        if ($entity->schacHomeOrganization) {
            $cortoEntity['SchacHomeOrganization'] = $entity->schacHomeOrganization;
        }

        $cortoEntity['SpsWithoutConsent'] = $entity->spsEntityIdsWithoutConsent;
        $cortoEntity['isHidden'] = $entity->hidden;

        $cortoEntity['shibmd:scopes'] = array();
        foreach ($entity->shibMdScopes as $scope) {
            $cortoEntity['shibmd:scopes'][] = array(
                'allowed' => $scope->allowed,
                'regexp'  => $scope->regexp,
            );
        }

        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return array
     */
    private function translateCommon(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        if ($entity->publishInEdugain) {
            $cortoEntity['PublishInEdugain'] = true;
        }
        if ($entity->publishInEduGainDate) {
            $cortoEntity['PublishInEdugainDate'] = $entity->publishInEduGainDate->format(DateTime::W3C);
        }
        if ($entity->disableScoping) {
            $cortoEntity['DisableScoping'] = true;
        }
        if ($entity->additionalLogging) {
            $cortoEntity['AdditionalLogging'] = $entity->additionalLogging;
        }
        $cortoEntity = $this->translateCommonCertificates($entity, $cortoEntity);
        if ($entity->logo) {
            $cortoEntity['Logo'] = array(
                'Height' => $entity->logo->height,
                'Width'  => $entity->logo->width,
                'URL'    => $entity->logo->url,
            );
        }
        if ($entity->requestsMustBeSigned) {
            $cortoEntity['AuthnRequestsSigned'] = $entity->requestsMustBeSigned;
        }
        if ($entity->nameIdFormat) {
            $cortoEntity['NameIDFormat'] = $entity->nameIdFormat;
        }
        $cortoEntity['NameIDFormats'] = $entity->nameIdFormats;
        $cortoEntity['WorkflowState'] = $entity->workflowState;

        $cortoEntity = $this->translateContactPersons($entity, $cortoEntity);
        $cortoEntity = $this->translateSingleLogoutServices($entity, $cortoEntity);
        $cortoEntity = $this->translateOrganization($entity, $cortoEntity);
        $cortoEntity = $this->translateKeywords($entity, $cortoEntity);
        $cortoEntity = $this->translateName($entity, $cortoEntity);
        $cortoEntity = $this->translateDescription($entity, $cortoEntity);
        $cortoEntity = $this->translateDisplayName($entity, $cortoEntity);
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return array
     */
    private function translateCommonCertificates(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        $cortoEntity['certificates'] = array();
        if (isset($entity->certificates[0])) {
            $cortoEntity['certificates']['public'] = $entity->certificates[0]->toPem();
        }
        if (isset($entity->certificates[1])) {
            $cortoEntity['certificates']['public-fallback'] = $entity->certificates[1]->toPem();
        }
        if (isset($entity->certificates[2])) {
            $cortoEntity['certificates']['public-fallback2'] = $entity->certificates[2]->toPem();
            return $cortoEntity;
        }
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return mixed
     */
    private function translateOrganization(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        // @codingStandardsIgnoreStart
        if ($entity->organizationEn) {
            $this->mapMultilang($entity->organizationEn->name       , $cortoEntity, 'Organization', 'Name'       , 'en');
            $this->mapMultilang($entity->organizationEn->displayName, $cortoEntity, 'Organization', 'DisplayName', 'en');
            $this->mapMultilang($entity->organizationEn->url        , $cortoEntity, 'Organization', 'URL'        , 'en');
        }

        if ($entity->organizationNl) {
            $this->mapMultilang($entity->organizationNl->name       , $cortoEntity, 'Organization', 'Name'       , 'nl');
            $this->mapMultilang($entity->organizationNl->displayName, $cortoEntity, 'Organization', 'DisplayName', 'nl');
            $this->mapMultilang($entity->organizationNl->url        , $cortoEntity, 'Organization', 'URL'        , 'nl');
        }
        // @codingStandardsIgnoreEnd
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return mixed
     */
    private function translateKeywords(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        if ($entity->keywordsNl) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Keywords', 'nl');
        }

        if ($entity->keywordsEn) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Keywords', 'nl');
        }
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return mixed
     */
    private function translateName(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        if ($entity->nameNl) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Name', 'nl');
        }

        if ($entity->nameEn) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Name', 'en');
        }
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return mixed
     */
    private function translateDescription(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        if ($entity->descriptionNl) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Description', 'nl');
        }

        if ($entity->descriptionEn) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'Description', 'nl');
        }
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return array
     */
    private function translateDisplayName(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        if ($entity->displayNameNl) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'DisplayName', 'nl');
        }

        if ($entity->displayNameEn) {
            $this->mapMultilang($entity->keywordsNl, $cortoEntity, 'DisplayName', 'nl');
        }
        return $cortoEntity;
    }

    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return array
     */
    private function translateSingleLogoutServices(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        foreach ($entity->singleLogoutServices as $service) {
            if (!isset($cortoEntity['SingleLogoutService'])) {
                $cortoEntity['SingleLogoutService'] = array();
            }

            $cortoEntity[] = array(
                'Binding' => $service->binding,
                'Location' => $service->location,
            );
        }
        return $cortoEntity;
    }


    /**
     * @param AbstractConfigurationEntity $entity
     * @param array $cortoEntity
     * @return array
     */
    private function translateContactPersons(AbstractConfigurationEntity $entity, array $cortoEntity)
    {
        $cortoEntity['ContactPersons'] = array();
        foreach ($entity->contactPersons as $contactPerson) {
            $cortoEntity['ContactPersons'][] = array(
                'ContactType' => $contactPerson->contactType,
                'EmailAddress' => $contactPerson->emailAddress,
                'GivenName' => $contactPerson->givenName,
                'SurName' => $contactPerson->surName,
            );
        }
        if (empty($cortoEntity['ContactPersons'])) {
            unset($cortoEntity['ContactPersons']);
            return $cortoEntity;
        }
        return $cortoEntity;
    }

    /**
     * Given:
     * $example = array();
     * mapMultilang(1, $example, 'a', 'b', 'c')
     * print_r($example);
     *
     * Gives:
     * Array (
     *  [a] => Array(
     *    [b] => Array(
     *      [c] => 1
     *     )
     *   )
     * )
     *
     * @param mixed $value
     * @param array $to
     */
    private function mapMultilang($value, array &$to)
    {
        $path = array_slice(func_get_args(), 2);
        while (count($path) > 1) {
            $key = array_shift($path);
            if (!isset($to[$key])) {
                $to[$key] = array();
            }
            $to = &$to[$key];
        }
        $to = $value;
    }
}
