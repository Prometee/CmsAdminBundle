<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

use Cms\Bundle\AdminBundle\Controller\BaseAdminController;
use Cms\Bundle\AdminBundle\Controller\Traits\SortableControllerTrait;

class SortableControllerExtension extends BaseAdminController {

	protected function buildController() {
		parent::buildController();
		
		$class_name = substr($this->doctrine_namespace, strpos($this->doctrine_namespace, ':') + 1);
		
		$this->route_order = ($this->route_order != 'cms_foo_admin_foo_order')
				? $this->route_order
				: $this->route_prefix . '_' . $this->translation_prefix . '_order';
		
		if (!$this->get('templating')->exists($this->template_order)) {
			$template_order = $this->bundle_name . ':Admin' . $class_name . ':order.html.twig';
			$this->template_order = $this->get('templating')->exists($template_order) ? $template_order : $this->default_template_order;
		}
		
		$this->addDefaultRenderParameter('route_order');
		$this->addDefaultRenderParameter('template_order');
	}
	
	use SortableControllerTrait;
}

?>
