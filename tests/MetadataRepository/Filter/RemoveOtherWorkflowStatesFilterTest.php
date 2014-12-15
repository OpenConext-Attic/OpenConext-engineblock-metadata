<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Mockery;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\Utils;
use PHPUnit_Framework_TestCase;

/**
 * Class RemoveDisallowedIdentityProvidersFilter
 *
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class RemoveOtherWorkflowStatesTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveOtherWorkflowState()
    {
        $prodSp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://prod.sp.example.edu', 'workflowState' => ServiceProvider::WORKFLOW_STATE_PROD)
        );
        $filter = new RemoveOtherWorkflowStatesFilter($prodSp);

        $prodIdp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://prod.idp.example.edu', 'workflowState' => ServiceProvider::WORKFLOW_STATE_PROD)
        );
        $this->assertNotNull($filter->filterRole($prodIdp));

        $testIdp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://test.idp.example.edu', 'workflowState' => ServiceProvider::WORKFLOW_STATE_PROD)
        );
        $this->assertNotNull($filter->filterRole($testIdp));

        $prodSp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://prod.sp.example.edu', 'workflowState' => ServiceProvider::WORKFLOW_STATE_PROD)
        );
        $this->assertNotNull($filter->filterRole($prodSp));

        $testSp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://test.sp.example.edu', 'workflowState' => ServiceProvider::WORKFLOW_STATE_TEST)
        );
        $this->assertNull($filter->filterRole($testSp));

        $buggyIdp = Utils::instantiate(
            'OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider',
            array('entityId' => 'https://buggy.idp.example.edu', 'workflowState' => '')
        );
        $this->assertNull($filter->filterRole($buggyIdp));
    }
}
