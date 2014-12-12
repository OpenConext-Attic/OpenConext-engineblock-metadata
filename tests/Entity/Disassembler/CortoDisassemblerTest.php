<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Disassembler;

use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use PHPUnit_Framework_TestCase;

/**
 * Class CortoDisassemblerTest
 * @package OpenConext\Component\EngineBlockMetadata\Entity\Disassembler
 */
class CortoDisassemblerTest extends PHPUnit_Framework_TestCase
{
    public function testSpDisassemble()
    {
        $serviceProvider = new ServiceProvider('https://sp.example.edu');
        $serviceProvider->displayNameNl = 'DisplayName';
        $serviceProvider->displayNameEn = 'DisplayName';
        $serviceProvider->isTransparentIssuer = true;
        $serviceProvider->displayUnconnectedIdpsWayf = true;
        $serviceProvider->implicitVoId = 'implicit';
        $serviceProvider->isConsentRequired = false;
        $serviceProvider->skipDenormalization = true;

        $disassembler = new CortoDisassembler();
        $cortoServiceProvider = $disassembler->translateServiceProvider($serviceProvider);

        $this->assertEquals($serviceProvider->entityId              , $cortoServiceProvider['EntityID']);
        $this->assertEmpty($cortoServiceProvider['certificates']);
        $this->assertEquals($serviceProvider->supportedNameIdFormats, $cortoServiceProvider['NameIDFormats']);
        $this->assertEquals($serviceProvider->workflowState         , $cortoServiceProvider['WorkflowState']);
        $this->assertEquals($serviceProvider->displayNameNl         , $cortoServiceProvider['DisplayName']['nl']);
        $this->assertEquals($serviceProvider->displayNameNl         , $cortoServiceProvider['DisplayName']['en']);
        $this->assertEquals('yes'                                   , $cortoServiceProvider['TransparentIssuer']);
        $this->assertEquals('yes'                                   , $cortoServiceProvider['DisplayUnconnectedIdpsWayf']);
        $this->assertEquals($serviceProvider->implicitVoId          , $cortoServiceProvider['VoContext']);
        $this->assertEquals($serviceProvider->isConsentRequired     , $cortoServiceProvider['NoConsentRequired']);
        $this->assertEquals($serviceProvider->skipDenormalization   , $cortoServiceProvider['SkipDenormalization']);
    }

    public function testIdpDisassemble()
    {
        $identityProvider = new IdentityProvider('https://idp.example.edu');

        $disassembler = new CortoDisassembler();
        $cortoIdentityProvider = $disassembler->translateIdentityProvider($identityProvider);

        $this->assertEquals($identityProvider->entityId, $cortoIdentityProvider['EntityID']);
        $this->assertEmpty($cortoIdentityProvider['certificates']);
        $this->assertEquals($identityProvider->supportedNameIdFormats, $cortoIdentityProvider['NameIDFormats']);
        $this->assertEquals($identityProvider->workflowState, $cortoIdentityProvider['WorkflowState']);
        $this->assertEquals($identityProvider->guestQualifier, $cortoIdentityProvider['GuestQualifier']);
        $this->assertEquals($identityProvider->spsEntityIdsWithoutConsent, $cortoIdentityProvider['SpsWithoutConsent']);
        $this->assertEquals($identityProvider->hidden, $cortoIdentityProvider['isHidden']);
        $this->assertEmpty($cortoIdentityProvider['shibmd:scopes']);
    }
}
