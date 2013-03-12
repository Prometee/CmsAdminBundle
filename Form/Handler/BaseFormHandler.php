<?php

namespace Cms\Bundle\AdminBundle\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

abstract class BaseFormHandler {

    protected $request_method = 'POST';
    protected $request;
    protected $em;

    public function __construct(Request $request, EntityManager $em) {
        $this->request = $request;
        $this->em = $em;
    }

}

?>
