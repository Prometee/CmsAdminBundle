<?php

namespace Cms\Bundle\AdminBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\NotValidException;

abstract class BaseEntityFormHandler extends BaseFormHandler {
	
    protected $class_name = 'Cms\Bundle\AdminBundle\Entity\Foo';

    public function process(Form $form, Controller $controller) {

        if (!$form->getData() instanceof $this->class_name) {
            throw new \Exception('This entity is not an instance of ' . $this->class_name);
        } else {
            if ($this->request_method == $this->request->getMethod()) {
                $form->bindRequest($this->request);

                if ($form->isValid()) {

                    $this->preSave($form, $controller);

                    $this->em->persist($form->getData());
                    $this->em->flush();

                    $this->postSave($form, $controller);

                    return true;
                } else {
                    throw new NotValidException('This form is not valid');
                }
            }
            return false;
        }
    }

    protected function preSave(Form $form, Controller $controller) {
        
    }

    protected function postSave(Form $form, Controller $controller) {
        
    }

}
