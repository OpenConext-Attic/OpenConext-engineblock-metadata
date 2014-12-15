<?php

namespace OpenConext\Component\EngineBlockMetadata;

use InvalidArgumentException;
use PHPUnit_Framework_Error;
use PHPUnit_Framework_TestCase;

/**
 * Class AttributeReleasePolicy
 * @package OpenConext\Component\EngineBlockMetadata
 */
class AttributeReleasePolicyTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $policy = new AttributeReleasePolicy(
            array(
                'a'=>array('*'),
                'b'=>array('b'),
                'c'=>array('c*'),
                'd'=>array('*d'),
            )
        );
        $this->assertEquals(array('a', 'b', 'c', 'd'), $policy->getAttributeNames());
        $this->assertTrue($policy->hasAttribute('a'));
        $this->assertFalse($policy->hasAttribute('z'));
        $this->assertTrue($policy->isAllowed('a', 'a'));
        $this->assertTrue($policy->isAllowed('b', 'b'));
        $this->assertFalse($policy->isAllowed('b', 'babe'));
        $this->assertTrue($policy->isAllowed('c', 'cat'));
        $this->assertFalse($policy->isAllowed('c', 'dad'));
        // Wildcard matching at the end only:
        $this->assertFalse($policy->isAllowed('d', 'tricked'));
    }

    public function testEmptyInstantiation()
    {
        $policy = new AttributeReleasePolicy(array());
        $this->assertEmpty($policy->getAttributeNames());
        $this->assertFalse($policy->isAllowed('a', 'a'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testNullInstantiation()
    {
        new AttributeReleasePolicy(null);
    }

    public function testInvalidInstantiation()
    {
        $e = null;
        try {
            new AttributeReleasePolicy(array('a'=>'b'));
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);

        $e = null;
        try {
            new AttributeReleasePolicy(array(array('b')));
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);

        $e = null;
        try {
            new AttributeReleasePolicy(array('a'=>array(1)));
        } catch (InvalidArgumentException $e) {
        }
        $this->assertNotNull($e);
    }
}
