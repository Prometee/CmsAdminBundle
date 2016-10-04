<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class BaseAdminGroup {

    /**
	 * @var string
	 * 
     * @Assert\Choice(callback="getActionsValues")
	 * @Assert\NotBlank(message="global.form_action.group.not_blank");
     */
    public $action;

    /**
     * @var array
     * @Assert\Count(min=1)
     */
    public $ids = array();

    protected static $actions = array(
        'global.form_action.group.none' => '',
        'global.form_action.group.delete' => 'delete'
    );

    public static function getActions() {
        return static::$actions;
    }

    public static function getActionsValues() {
        return array_values(static::getActions());
    }

}
