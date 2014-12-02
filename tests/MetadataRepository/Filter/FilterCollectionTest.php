<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Expression;
use Mockery;
use PHPUnit_Framework_TestCase;

/**
 * Class FilterCollectionTest
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter
 */
class FilterCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testFilterRoleFailure()
    {
        $mockFilter = Mockery::mock(
            'OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface'
        );
        $mockFilter->shouldReceive('filterRole')->andReturnNull();
        $mockFilter->shouldReceive('__toString')->andReturn('MockFilter');


        $mockRole = Mockery::mock(
            'OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole'
        );

        $collection = new FilterCollection();
        $collection->add($mockFilter);
        $this->assertNull($collection->filterRole($mockRole));
        $this->assertEquals('MockFilter', $collection->getDisallowedByFilter());
        $this->assertEquals('[MockFilter]', (string)$collection);
    }

    public function testFilterExport()
    {
        $mockFilter = Mockery::mock(
            'OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter\FilterInterface'
        );
        $mockFilter->shouldReceive('toExpression')->andReturn(Criteria::expr()->isNull('entityId'));
        $mockFilter->shouldReceive('toQueryBuilder');

        $collection = new FilterCollection();
        $collection->add($mockFilter);

        $this->assertTrue($collection->toExpression() instanceof Expression);
        $this->assertTrue($collection->toCriteria() instanceof Criteria);
        $queryBuilderMock = Mockery::mock('Doctrine\ORM\QueryBuilder');
        $this->assertEquals($queryBuilderMock, $collection->toQueryBuilder($queryBuilderMock));
    }
}
