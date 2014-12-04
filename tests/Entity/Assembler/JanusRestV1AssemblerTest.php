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
use PHPUnit_Framework_TestCase;
use RuntimeException;

/**
 * Class JanusRestV1Assembler
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Translator
 * @SuppressWarnings(PMD.TooManyMethods)
 * @SuppressWarnings(PMD.CouplingBetweenObjects)
 */
class JanusRestV1AssemblerTest extends PHPUnit_Framework_TestCase
{
    public function testAssembleEmptyEntityInfo()
    {
        $entity = array();

        $assembler = new JanusRestV1Assembler();
        $this->assertNull($assembler->assemble('https://entityId', $entity));
    }
}
