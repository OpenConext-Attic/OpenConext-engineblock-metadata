<?php

namespace OpenConext\Component\EngineBlockMetadata\X509;

use PHPUnit_Framework_TestCase;

/**
 * Class X509KeyPairTest
 * @package OpenConext\Component\EngineBlockMetadata\X509
 */
class X509KeyPairTest extends PHPUnit_Framework_TestCase
{
    public function testNullInput()
    {
        $keyPair = new X509KeyPair();
        $this->assertNull($keyPair->getCertificate());
        $this->assertNull($keyPair->getPrivateKey());
    }
}
