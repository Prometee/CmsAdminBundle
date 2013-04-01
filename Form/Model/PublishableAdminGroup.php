<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

class PublishableAdminGroup extends BaseAdminGroup {
	
	public static function getActions() {
		$actions = parent::getActions();
		$actions['publish'] = 'global.form_action.group.publish';
		$actions['unpublish'] = 'global.form_action.group.unpublish';
		
		return $actions;
    }
}

?>
