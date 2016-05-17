<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AdminDashboardController extends Controller {
	
	/**
	 * @Template
	 */
    public function indexAction(Request $request) {
	    $entity_list = array();
	    return array('entity_list'=>$entity_list);
    }
}
