<?php

namespace Cms\Bundle\AdminBundle\Controller\Traits;

use Symfony\Component\Form\Exception\NotValidException;

trait SortableControllerTrait {

        protected $route_order = 'cms_foo_admin_foo_order';
	protected $template_order = 'FooBundle:AdminFoo:order.html.twig';
	protected $order_group_form_type_name = 'Cms\Bundle\AdminBundle\Form\Type\SortableAdminGroupFormType';
	protected $order_group_object_name = 'Cms\Bundle\AdminBundle\Form\Model\SortableAdminGroup';

	private $default_template_order = 'CmsAdminBundle:CRUD:order.html.twig';

	protected function getOrderGroupForm($entity) {
		return $this->createForm(new $this->order_group_form_type_name(), $entity, array(
			'data_class' => $this->order_group_object_name,
			'translation_domain' => 'CmsAdminBundle'
		));
	}

	protected function redirectOrderError($process_action) {
		return $this->redirect($this->generateUrl($this->route_order));
	}

	protected function redirectOrderSuccess($process_action) {
		return $this->redirect($this->generateUrl($this->route_order));
	}

	public function orderAction() {
        $form = $this->getOrderGroupForm(new $this->order_group_object_name());

        $entity_list = $this->getClassRepository()->findBy(array(), array('position'=>'ASC'));

        $handler = new $this->group_form_handler_name(
			$this->getRequest(),
			$this->getDoctrine()->getManager()
        );

		try {
            $process = $handler->process($form, $this->getRequest()->get('ids'));
        } catch (NotValidException $e) {
			$process = false;
			$action = $form->getData()->action;
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.group.'.$action, array(), $this->bundle_name
                )
            );

			return $this->redirectOrderError($action);
        }

        if ($process) {
            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                $this->translation_prefix . '.flash.success.group.'.$process, array(), $this->bundle_name)
            );

			return $this->redirectOrderSuccess($process);
        }

        return $this->render($this->template_order, array(
			'entity_list'=>$entity_list,
			'groupForm' => $form->createView(),
			'route_form_action' => $this->route_order
		));
    }
}

?>
