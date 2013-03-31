<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

use Cms\Bundle\AdminBundle\Controller\BaseAdminController;

interface ControllerExtensionInterface {
	public function __construct(BaseAdminController $controller);
	
	public function configure();
}

?>
