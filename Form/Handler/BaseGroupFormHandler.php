<?php

namespace Cms\Bundle\AdminBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

abstract class BaseGroupFormHandler extends BaseFormHandler {

    protected $repository_name = 'CmsAdminBundle:Foo';

    public function process(Form $form, $ids) {
	if ($this->request_method == $this->request->getMethod()) {
	    $form->bindRequest($this->request);

	    if (!is_array($ids) || count($ids) <= 0)
			return false;
            
	    if ($form->isValid()) {
			$data = $form->getData();

			$ids_filtered = array();
			foreach ($ids as $id) {
				$test = (int) $id;
				if (is_int($test))
				array_push($ids_filtered, $test);
			}

			$repository = $this->em->getRepository($this->repository_name);
			if (method_exists($repository, $data->action.'Group')) {
				$repository->{$data->action.'Group'}($ids_filtered);
				return $data->action;
			} else {
						throw new \BadMethodCallException('The method '.get_class($repository).'::'.$data->action.'Group doesn\'t exist !');
					}
			}
		}
		return false;
    }

}