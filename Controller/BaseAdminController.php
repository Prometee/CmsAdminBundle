<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseAdminController extends Controller {

	//Must to be implemanted by the master class
	protected $doctrine_namespace = "AdminBundle:Foo";
	protected $translation_prefix = 'foo';
	protected $bundle_name = 'AdminBundle';
	protected $class_repository = 'Cms\Bundle\AdminBundle\Entity\Foo';
	protected $object_name = 'Foo';
	protected $form_type_name = 'FooFormType';
	protected $form_handler_name = 'FooFormHandler';
	protected $filter_object_name = 'FooFilter';
	protected $filter_form_type_name = 'FooFilterFormType';
	protected $filter_form_handler_name = 'FooFilterFormHandler';
	protected $group_object_name = 'FooGroup';
	protected $group_form_type_name = 'FooGroupFormType';
	protected $group_form_handler_name = 'FooGroupFormHandler';
	protected $route_index = 'np_foo_foo_index';
	protected $route_new = 'np_foo_foo_new';
	protected $route_edit = 'np_foo_foo_edit';
	protected $route_show = 'np_foo_foo_show';
	protected $route_delete = 'np_foo_foo_delete';
	protected $route_publish = 'np_foo_foo_publish_toggle';
	protected $route_groupprocess = 'np_foo_foo_groupprocess';
	//Default values
	protected $template_index = 'AdminBundle:CRUD:index.html.twig';
	protected $template_new = 'AdminBundle:CRUD:new.html.twig';
	protected $template_edit = 'AdminBundle:CRUD:edit.html.twig';
	protected $template_show = 'AdminBundle:CRUD:show.html.twig';
	protected $max_per_page = 10;

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->buildController();
	}

	public function buildController() {

		if (false !== $pos = strpos($this->doctrine_namespace, ':')) {
			$this->bundle_name = substr($this->doctrine_namespace, 0, $pos);
			$class_name = substr($this->doctrine_namespace, $pos + 1);
			$class_path = $this->getDoctrine()->getEntityNamespace($this->bundle_name);
			if (!class_exists($this->object_name))
				$this->object_name = $class_path . '\\' . $class_name;

			$class_path = preg_replace('#Entity$#', '', $class_path);
			if (!class_exists($this->form_type_name))
				$this->form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'FormType';
			if (!class_exists($this->form_handler_name))
				$this->form_handler_name = $class_path . 'Form\\Handler\\' . $class_name . 'FormHandler';

			if (!class_exists($this->filter_object_name))
				$this->filter_object_name = $class_path . 'Form\\Model\\' . $class_name . 'Filter';
			if (!class_exists($this->filter_form_type_name))
				$this->filter_form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'FilterFormType';
			if (!class_exists($this->filter_form_handler_name))
				$this->filter_form_handler_name = $class_path . 'Form\\Handler\\' . $class_name . 'FilterFormHandler';

			if (!class_exists($this->group_object_name))
				$this->group_object_name = $class_path . 'Form\\Model\\' . $class_name . 'Group';
			if (!class_exists($this->group_form_type_name))
				$this->group_form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'GroupFormType';
			if (!class_exists($this->group_form_handler_name))
				$this->group_form_handler_name = $class_path . 'Form\\Handler\\' . $class_name . 'GroupFormHandler';

			$this->translation_prefix = $this->container->underscore($class_name);
			$route_prefix = $this->container->underscore(preg_replace('#Bundle$#', '', $this->bundle_name));
			$this->route_index = ($this->route_index != 'np_foo_foo_index') ? $this->route_index : $route_prefix . '_' . $this->translation_prefix . '_index';
			$this->route_new = ($this->route_new != 'np_foo_foo_new') ? $this->route_new : $route_prefix . '_' . $this->translation_prefix . '_new';
			$this->route_edit = ($this->route_edit != 'np_foo_foo_edit') ? $this->route_new : $route_prefix . '_' . $this->translation_prefix . '_edit';
			$this->route_show = ($this->route_show != 'np_foo_foo_show') ? $this->route_new : $route_prefix . '_' . $this->translation_prefix . '_show';
			$this->route_delete = ($this->route_delete != 'np_foo_foo_delete') ? $this->route_new : $route_prefix . '_' . $this->translation_prefix . '_delete';
			$this->route_publish = ($this->route_publish != 'np_foo_foo_publish_toggle') ? $this->route_new : $route_prefix . '_' . $this->translation_prefix . '_publish_toggle';
			$this->route_groupprocess = ($this->route_groupprocess != 'np_foo_foo_groupprocess') ? $this->route_groupprocess : $route_prefix . '_' . $this->translation_prefix . '_groupprocess';
		}
	}

	protected function getClassRepository() {
		return $this->getDoctrine()->getRepository($this->object_name);
	}

	protected function getGroupForm($entity) {
		return $this->createForm(new $this->group_form_type_name($this->get('translator')), $entity);
	}

	protected function getForm($entity) {
		return $this->createForm(new $this->form_type_name(), $entity);
	}

	protected function getFilterForm($entity) {
		if (class_exists($this->filter_form_type_name)) {
			return $this->createForm(new $this->filter_form_type_name(), $entity);
		} else {
			return null;
		}
	}

	protected function retrieveEntity($id) {
		return $this->getClassRepository()->findOneById($id);
	}

	public function indexAction() {
		$form = $this->getGroupForm(new $this->group_object_name());

		$request = $this->getRequest();

		$filter_entity = (class_exists($this->filter_object_name)) ? new $this->filter_object_name() : null;
		$filter = $this->getFilterForm($filter_entity);

		$query = $this->getClassRepository()->findAll();

		$pagination = $this->get('knp_paginator')
				->paginate($query, $request->query->get('page', 1), $this->max_per_page);

		return $this->render($this->template_index, array(
					'filter' => (($filter) ? $filter->createView() : null),
					'pagination' => $pagination,
					'groupForm' => $form->createView(),
					'translation_prefix' => $this->translation_prefix,
					'bundle_name' => $this->bundle_name,
					'route_new' => $this->route_new,
					'route_index' => $this->route_index,
					'route_edit' => $this->route_edit,
					'route_show' => $this->route_show,
					'route_delete' => $this->route_delete,
					'route_publish' => $this->route_publish,
					'route_form_action' => $this->route_groupprocess
		));
	}

	public function processFilter($filter, $filter_entity) {

		if ('POST' == $this->getRequest()->getMethod()) {
			$handler = new $this->filter_form_handler_name(
					$this->getRequest(), $this->getDoctrine()->getEntityManager()
			);

			return $handler->process($filter, $this);
		} else {
			return $this->getClassRepository()->findAllQuery();
		}
	}

	public function showAction($id) {
		$entity = $this->getClassRepository()->findOneBy(array('id' => $id));

		return $this->render($this->template_show, array(
					'entity' => $entity,
					'route_index' => $this->route_index,
					'route_edit' => $this->route_edit
		));
	}

	public function newAction() {
		$entity = new $this->object_name();

		$form = $this->getForm($entity);

		$handler = new $this->form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getEntityManager()
		);

		if ($handler->process($form, $this)) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.new', array('%name%' => $entity), $this->bundle_name)
			);

			if ($this->getRequest()->get('save_and_add') != null) {
				return $this->redirect($this->generateUrl($this->route_new));
			}
			return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
		}

		return $this->render($this->template_new, array(
					'form' => $form->createView(),
					'route_form_action' => $this->route_new,
					'route_index' => $this->route_index,
					'translation_prefix' => $this->translation_prefix,
					'bundle_name' => $this->bundle_name
		));
	}

	public function editAction($id) {
		$entity = $this->retrieveEntity($id);

		$form = $this->getForm($entity);

		$handler = new $this->form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getEntityManager()
		);

		if ($handler->process($form, $this)) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.edit', array('%name%' => $entity), $this->bundle_name)
			);

			return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
		}

		return $this->render($this->template_edit, array(
					'form' => $form->createView(),
					'entity' => $entity,
					'route_form_action' => $this->route_edit,
					'route_index' => $this->route_index,
					'route_delete' => $this->route_delete,
					'route_show' => $this->route_show,
					'translation_prefix' => $this->translation_prefix,
					'bundle_name' => $this->bundle_name
		));
	}

	public function publish_toggleAction($id) {
		$object = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getEntityManager();
		$object->getPublished() ? $object->setPublished(0) : $object->setPublished(1);
		$em->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$object->getPublished() ? $this->translation_prefix . '.flash.success.publish' : $this->translation_prefix . '.flash.success.unpublish', array(), $this->bundle_name)
		);

		return $this->redirect($this->generateUrl($this->route_index));
	}

	public function publishAction($id) {
		$object = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getEntityManager();
		$object->setPublished(1);
		$em->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.publish', array(), $this->bundle_name)
		);

		return $this->redirect($this->generateUrl($this->route_index));
	}

	public function unpublishAction($id) {
		$object = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getEntityManager();
		$object->setPublished(0);
		$em->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.unpublish', array(), $this->bundle_name)
		);

		return $this->redirect($this->generateUrl($this->route_index));
	}

	public function deleteAction($id) {
		$object = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getEntityManager();
		$em->remove($object);
		$em->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.delete', array(), $this->bundle_name)
		);

		return $this->redirect($this->generateUrl($this->route_index));
	}

	public function groupProcessAction() {
		$form = $this->getGroupForm(new $this->group_object_name());

		$handler = new $this->group_form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getEntityManager()
		);

		$process = $handler->process($form, $this->getRequest()->get('ids'));
		if ($process != false) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.group.' . $process, array(), $this->bundle_name)
			);
		}

		return $this->indexAction();
		//return $this->redirect($this->generateUrl($this->route_index));
	}

}
