<?php

namespace OpenConext\Component\EngineBlockMetadata\Entity\Role;

use Doctrine\ORM\Mapping as ORM;
use OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole;

/**
 * @ORM\Entity
 * @ORM\Table(name="sso_role_contact_person")
 */
class ContactPerson
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var AbstractRole
     *
     * @ORM\ManyToOne(
     *  targetEntity="OpenConext\Component\EngineBlockMetadata\Entity\AbstractRole",
     *  inversedBy="contactPersons"
     * )
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    protected $contactType;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string")
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="given_name", type="string")
     */
    protected $givenName;

    /**
     * @var string
     *
     * @ORM\Column(name="sur_name", type="string")
     */
    protected $surName;
}
