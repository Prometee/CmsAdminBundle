<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminDashboardController extends Controller {
	
	/**
	 * @Template
	 */
    public function indexAction() {
	    $entity_list = array();
	    return array('entity_list'=>$entity_list);
    }
}
