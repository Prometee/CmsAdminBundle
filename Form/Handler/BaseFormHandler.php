<?php

namespace Cms\Bundle\CoreBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

abstract class BaseFormHandler {

    protected $request;
    protected $em;
    protected $request_method = 'POST';
    protected $class_name = 'Cms\Bundle\CoreBundle\Entity\Foo';

    public function __construct(Request $request, EntityManager $em) {
        $this->request = $request;
        $this->em = $em;
    }

    public function process(Form $form, $controller) {

        if (!$form->getData() instanceof $this->class_name) {
            throw new \Exception('This entity is not an instance of ' . $this->class_name);
        } else {
            if ($this->request_method == $this->request->getMethod()) {
                $form->bindRequest($this->request);

                if ($form->isValid()) {

                    $this->preSave($form, $form->getData(), $controller);

                    $this->em->persist($form->getData());
                    $this->em->flush();

                    $this->postSave($form, $form->getData(), $controller);

                    return true;
                }
            }
            return false;
        }
    }

    protected function preSave(Form $form, $entity, $controller) {
        
    }

    protected function postSave(Form $form, $entity, $controller) {
        
    }

}
