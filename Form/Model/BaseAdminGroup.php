<?php

namespace Cms\Bundle\CoreBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

abstract class BaseAdminGroup
{
    /**
     * @Assert\Choice(callback = "getActions")
     */
    public $action;
    
    public static function getActions()
    {
        return array(
            'none',
            'delete'
        );
    }
}
