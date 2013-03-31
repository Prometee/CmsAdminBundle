<?php

namespace Cms\Bundle\AdminBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Form\Exception\NotValidException;

class BaseEntityFormHandler extends BaseFormHandler {

    protected $class_name = 'Cms\Bundle\AdminBundle\Entity\Foo';

    public function process(FormInterface $form, ContainerAwareInterface $controller) {

        if (!$form->getData() instanceof $this->class_name) {
            throw new \Exception('This entity is not an instance of ' . $this->class_name);
        } else {
            if ($this->request_method == $this->request->getMethod()) {
                $form->bind($this->request);

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

    protected function preSave(FormInterface $form, ContainerAwareInterface $controller) {

    }

    protected function postSave(FormInterface $form, ContainerAwareInterface $controller) {

    }

}
