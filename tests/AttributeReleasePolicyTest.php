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

    public function testArpWithSources()
    {
        $policy = new AttributeReleasePolicy(
            array(
                'a' => array('a'),
                'b' => array(
                    array(
                        'value' => 'b',
                        'source' => 'b',
                    ),
                ),
            )
        );

        $this->assertEquals(array('a', 'b'), $policy->getAttributeNames());
        $this->assertTrue($policy->hasAttribute('a'));
        $this->assertTrue($policy->hasAttribute('b'));
        $this->assertTrue($policy->isAllowed('a', 'a'));
        $this->assertTrue($policy->isAllowed('b', 'b'));
        $this->assertFalse($policy->isAllowed('a', 'b'));
        $this->assertFalse($policy->isAllowed('b', 'a'));
    }

    public function testInvalidArpWithSourceSpecification()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        new AttributeReleasePolicy(
            array(
                'b' => array(
                    array(
                        'source' => 'b',
                    ),
                ),
            )
        );
    }

    public function testAttributesEligibleForAggregation()
    {
        $policy = new AttributeReleasePolicy(
            array(
                'a' => array('a'),
                'b' => array(
                    array(
                        'value' => 'b',
                        'source' => 'b',
                    ),
                ),
                'c' => array(
                    array(
                        'value' => 'c',
                    ),
                ),
            )
        );

        $this->assertEquals(
            array(
                'b' => array(
                    array(
                        'value' => 'b',
                        'source' => 'b',
                    ),
                ),
            ),
            $policy->getRulesWithSourceSpecification()
        );
    }
    public function testGetSource()
    {
        $policy = new AttributeReleasePolicy(
            array(
                'a' => array('a'),
                'b' => array(
                    array(
                        'value' => 'b',
                        'source' => 'b',
                    ),
                ),
                'c' => array(
                    array(
                        'value' => 'c',
                    ),
                ),
            )
        );

        $this->assertEquals('idp', $policy->getSource('a'), 'Default source should equal idp');
        $this->assertEquals('b', $policy->getSource('b'));
        $this->assertEquals('idp', $policy->getSource('c'), 'Default source should equal idp');
    }
}
