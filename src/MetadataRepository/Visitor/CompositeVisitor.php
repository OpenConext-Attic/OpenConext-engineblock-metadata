<?php

namespace OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\ORM\QueryBuilder;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;
use OpenConext\Component\EngineBlockMetadata\Entity\IdentityProvider;
use OpenConext\Component\EngineBlockMetadata\Entity\ServiceProvider;
use OpenConext\Component\EngineBlockMetadata\MetadataRepository\Visitor\VisitorInterface;

/**
 * Class FilterCollection
 * @package OpenConext\Component\EngineBlockMetadata\MetadataRepository\Helper
 */
class CompositeVisitor implements VisitorInterface
{
    /**
     * @var VisitorInterface[]
     */
    private $visitors = array();

    /**
     * @param VisitorInterface $visitor
     * @return $this
     */
    public function enqueue(VisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function visitIdentityProvider(IdentityProvider $identityProvider)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitIdentityProvider($identityProvider);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitServiceProvider(ServiceProvider $serviceProvider)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitServiceProvider($serviceProvider);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitRole(AbstractRole $role)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->visitRole($role);
        }
    }
}
