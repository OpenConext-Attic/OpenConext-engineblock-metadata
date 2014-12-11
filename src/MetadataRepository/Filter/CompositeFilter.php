<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Filter;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * Class CompositeFilter
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Helper
 */
class CompositeFilter implements FilterInterface
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
     * @param AbstractRole[] $roles
     * @return AbstractRole[]
     */
    public function filterRoles($roles)
    {
        $newRoles = array();
        foreach ($roles as $key => $role) {
            $role = $this->filterRole($role);

            if (!$role) {
                continue;
            }

            $newRoles[$key] = $role;
        }
        return $newRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function filterRole(AbstractRole $role)
    {
        foreach ($this->filters as $filter) {
            $role = $filter->filterRole($role);

            if (!$role) {
                $this->disallowedByFilter = $filter->__toString();
                return null;
            }
        }
        return $role;
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return Criteria
     */
    public function toCriteria()
    {
        return Criteria::create()->where($this->toExpression());
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

    /**
     * @return string
     */
    public function getDisallowedByFilter()
    {
        return $this->disallowedByFilter;
    }
}