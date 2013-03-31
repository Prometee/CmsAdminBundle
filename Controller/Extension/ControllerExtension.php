<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

use Cms\Bundle\AdminBundle\Controller\BaseAdminController;

abstract class ControllerExtension implements ControllerExtensionInterface {
	
	protected $controller;
	
	public function __construct(BaseAdminController $controller) {
		$this->controller = $controller;
	}
	
	public function configure() {
		
	}
}
