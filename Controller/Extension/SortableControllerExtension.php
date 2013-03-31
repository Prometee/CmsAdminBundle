<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

use Symfony\Component\Form\Exception\NotValidException;

class SortableControllerExtension extends ControllerExtension {
	
    public $route_order = 'cms_foo_admin_foo_order';
	public $template_order = 'FooBundle:AdminFoo:order.html.twig';
	public $form_type_order_name = 'Cms\Bundle\AdminBundle\Form\Type\AdminOrderGroupFormType';
	
	private $default_template_order = 'CmsAdminBundle:CRUD:order.html.twig';

	public function configure() {
		
		$class_name = substr($this->controller->doctrine_namespace, strpos($this->controller->doctrine_namespace, ':') + 1);
		
		$this->route_order = ($this->route_order != 'cms_foo_admin_foo_order')
				? $this->route_order
				: $this->controller->route_prefix . '_' . $this->controller->translation_prefix . '_order';
		
		if (!$this->controller->get('templating')->exists($this->template_order)) {
			$template_order = $this->controller->bundle_name . ':Admin' . $class_name . ':order.html.twig';
			$this->template_order = $this->controller->get('templating')->exists($template_order) ? $template_order : $this->default_template_order;
		}
		
		$this->controller->AddDefaultRenderParameter('route_order');
		$this->controller->AddDefaultRenderParameter('template_order');
	}
	
	public function getOrderGroupForm($entity) {
		return $this->controller->createForm(new $this->form_type_order_name(), $entity);
	}
	
	public function redirectOrderError($process_action) {
		return $this->controller->redirect($this->controller->generateUrl($this->route_order));
	}
	
	public function orderAction() {
        $form = $this->getOrderGroupForm(new $this->controller->group_object_name());

        $entity_list = $this->controller->getClassRepository()->findBy(array(), array('position'=>'ASC'));

        $handler = new $this->controller->group_form_handler_name(
			$this->controller->getRequest(),
			$this->controller->getDoctrine()->getManager()
        );
		
		try {
            $process = $handler->process($form, $this->controller->getRequest()->get('ids'));
        } catch (NotValidException $e) {
			$action = $form->getData()->action;
            $this->controller->get('session')->setFlash('error', $this->controller->get('translator')->trans(
                    $this->controller->translation_prefix . '.flash.error.group.'.$action, array(), $this->controller->bundle_name
                )
            );
			
			return $this->controller->redirectOrderError($action);
        }

        if ($process != false) {
            $this->controller->get('session')->getFlashBag()->set('success', $this->controller->get('translator')->trans(
                $this->controller->translation_prefix . '.flash.success.group.'.$process, array(), $this->controller->bundle_name)
            );
        }

        return $this->controller->render($this->template_order, array(
			'entity_list'=>$entity_list,
			'groupForm' => $form->createView()
		));
    }
}

?>
