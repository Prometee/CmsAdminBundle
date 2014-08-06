<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class BaseAdminGroup {

    /**
	 * @var string
	 * 
     * @Assert\Choice(callback="getActionsKeys")
	 * @Assert\NotBlank(message="global.form_action.group.not_blank");
     */
    public $action;
    public $ids = array();

    protected static $actions = array(
        '' => 'global.form_action.group.none',
        'delete' => 'global.form_action.group.delete'
    );

    public static function getActions() {
        return static::$actions;
    }

    public static function getActionsKeys() {
        return array_keys(static::getActions());
    }

}
