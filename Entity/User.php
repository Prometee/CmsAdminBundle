<?php

namespace Cms\Bundle\AdminBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var array
     *
     * @Assert\Choice(callback = "getRolesChoicesKeys", multiple=true)
     */
    protected $roles;

    public static function getRolesChoices() {
        return array(
            'ROLE_ADMIN' => 'user.roles.admin',
            'ROLE_SUPER_ADMIN' => 'user.roles.super_admin'
        );
    }

    public static function getRolesChoicesKeys() {
        return array_keys(self::getRolesChoices());
    }
}