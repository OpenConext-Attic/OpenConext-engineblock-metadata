<?php

namespace OpenConext\Component\EngineBlockMetadata\Doctrine\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use SAML2_Certificate_X509;

/**
 * Class X509CertificatesCollectionType
 * @package OpenConext\Component\EngineBlockMetadata\Doctrine\Type
 */
class X509CertificatesCollectionType extends Type
{
    const NAME = 'x509_certificates_collection';
    const SEPARATOR = ' ';

    /**
     * {@inheritdoc}
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['length'] = MySqlPlatform::LENGTH_LIMIT_MEDIUMTEXT;
        return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!is_string($value)) {
            throw new \RuntimeException(self::NAME . ' database value is not a string');
        }

        return new ArrayCollection(
            array_map(
                function ($certData) {
                    return SAML2_Certificate_X509::createFromCertificateData($certData);
                },
                explode(self::SEPARATOR, $value)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof Collection) {
            throw new \RuntimeException(self::NAME . ' PHP value is not a Collection');
        }

        $result = "";
        $value->map(function (SAML2_Certificate_X509 $certificate) use (&$result) {
            $result .= self::SEPARATOR . $certificate['X509Certificate'];
        });

        if (strlen($result) > MySqlPlatform::LENGTH_LIMIT_MEDIUMTEXT) {
            // @todo make this a warning
            throw new \RuntimeException('Certificate collection too large for storage!');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }
}
