<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DashboardController extends Controller {
    /**
     * 
     * @Template()
     */
    public function indexAction() {
	    $this->entity_list = array();
	    return array('entity_list'=>$this->entity_list);
    }
}
