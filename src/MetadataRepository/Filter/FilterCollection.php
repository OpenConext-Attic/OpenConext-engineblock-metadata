<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Class FilterCollection
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Helper
 */
class FilterCollection implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = array();

    /**
     * @var string
     */
    private $disallowedByFilter;

    /**
     * @param FilterInterface $filter
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return string
     */
    public function getDisallowedByFilter()
    {
        return $this->disallowedByFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function filterRole(AbstractRole $entity)
    {
        foreach ($this->filters as $filter) {
            $entity = $filter->filterRole($entity);

            if (!$entity) {
                $this->disallowedByFilter = $filter->__toString();
                return null;
            }
        }
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function toQueryBuilder(QueryBuilder $queryBuilder)
    {
        foreach ($this->filters as $filter) {
            $filter->toQueryBuilder($queryBuilder);
        }
        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function toExpression()
    {
        $expressions = array();

        foreach ($this->filters as $filter) {
            $expressions[] = $filter->toExpression();
        }

        return new CompositeExpression(CompositeExpression::TYPE_AND, $expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $filterStrings = array();
        foreach ($this->filters as $filter) {
            $filterStrings[] = $filter->__toString();
        }

        return '[' . implode(', ', $filterStrings) . ']';
    }
}
