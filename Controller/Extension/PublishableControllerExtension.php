<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

use Cms\Bundle\AdminBundle\Controller\BaseAdminController;
use Cms\Bundle\AdminBundle\Controller\Traits\PublishableControllerTrait;

class PublishableControllerExtension extends BaseAdminController {

	protected function buildController() {
		parent::buildController();
        
        $this->group_object_name = $this->publish_group_object_name;
		
		$this->route_publish = ($this->route_publish != 'cms_foo_admin_foo_publish_toggle')
				? $this->route_publish
				: $this->route_prefix . '_' . $this->translation_prefix . '_publish_toggle';
		
		$this->addDefaultRenderParameter('route_publish');
	}
	
	use PublishableControllerTrait;
}
