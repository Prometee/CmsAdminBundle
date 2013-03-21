<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class BaseAdminGroup
{
    /**
     * @Assert\Choice(callback = "getActionsKeys")
     */
    public $action;
    
    public static function getActions()
    {
        return array(
            'none' => 'global.form_action.group.none',
            'delete' => 'global.form_action.group.delete'
        );
    }
	
	public static function getActionsKeys() {
		return array_keys(self::getActions());
	}
}
