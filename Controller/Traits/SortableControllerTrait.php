<?php

namespace Cms\Bundle\AdminBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;

trait SortableControllerTrait {

    protected $route_order = 'cms_foo_admin_foo_order';
    protected $route_order_process = 'cms_foo_admin_foo_order_process';
	protected $template_order = 'CmsAdminBundle:CRUD:order.html.twig';
	protected $order_group_form_type_name = 'Cms\Bundle\AdminBundle\Form\Type\SortableAdminGroupFormType';
	protected $order_group_object_name = 'Cms\Bundle\AdminBundle\Form\Model\SortableAdminGroup';

    protected function preConfigureTrait() {
        $this->available_template_names[] = 'order';
        $this->available_route_names[] = 'order';
        $this->available_route_names[] = 'order_process';

        $this->addDefaultRenderParameter('route_order');
        $this->addDefaultRenderParameter('template_order');
    }

	protected function getOrderGroupForm($entity, $form_options = array()) {
        $form_options = array_unique(array_merge(array(
                'data_class' => $this->order_group_object_name,
                'ids_class' => $this->doctrine_namespace,
                'translation_domain' => 'CmsAdminBundle'
            ),
            $form_options
        ));
		return $this->createForm(new $this->order_group_form_type_name(), $entity, $form_options);
	}

	protected function redirectOrderError() {
		return $this->redirect($this->generateUrl($this->route_order));
	}

	protected function redirectOrderSuccess() {
		return $this->redirect($this->generateUrl($this->route_order));
	}

    /**
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createOrderGroupForm($entity)
    {
        $form = $this->getOrderGroupForm($entity, array(
            'action' => $this->generateUrl($this->route_order_process),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit');

        return $form;
    }

    protected function retrieveOrderEntityList() {
        return $this->getClassRepository()->findBy(array(), array('position'=>'ASC'));
    }

	public function orderAction(Request $request) {
        $form = $this->createOrderGroupForm(new $this->order_group_object_name());

        $entity_list = $this->retrieveOrderEntityList();

        //For errors in order process
        $form->handleRequest($request);

        return $this->render($this->template_order, array(
			'entity_list'=>$entity_list,
			'groupForm' => $form->createView(),
			'route_form_action' => $this->route_order
		));
    }

    public function orderProcessAction(Request $request) {
        $form = $this->createOrderGroupForm(new $this->order_group_object_name());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $this->groupProcess($data->action, $data->ids);

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.success.group.'.$data->action, array(), $this->bundle_name)
            );

            return $this->redirectOrderSuccess();
        } else {
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.group.'.$form->getData()->action, array(), $this->bundle_name
                )
            );

            return $this->orderAction();
        }
    }
}
