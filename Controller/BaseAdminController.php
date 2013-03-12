<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Exception\NotValidException;

abstract class BaseAdminController extends Controller {

	//Must to be implemanted by the master class
	protected $doctrine_namespace = "CmsAdminBundle:Foo";
	protected $translation_prefix = 'foo';
	protected $translation_domain = 'CmsAdminBundle';
	protected $bundle_name = 'CmsAdminBundle';
	protected $class_repository = 'Cms\Bundle\AdminBundle\Entity\Foo';
	protected $entity_name = 'Foo';
	protected $form_type_name = 'FooFormType';
	protected $form_handler_name = 'FooFormHandler';
	protected $filter_object_name = 'FooFilter';
	protected $filter_form_type_name = 'FooFilterFormType';
	protected $filter_form_handler_name = 'FooFilterFormHandler';
	protected $group_object_name = 'Cms\Bundle\AdminBundle\Form\Model\BaseAdminGroup';
	protected $group_form_type_name = 'Cms\Bundle\AdminBundle\Form\Type\BaseAdminGroupFormType';
	protected $group_form_handler_name = 'FooGroupFormHandler';
	protected $route_prefix = '';
	protected $route_index = 'cms_foo_admin_foo_index';
	protected $route_new = 'cms_foo_admin_foo_new';
	protected $route_edit = 'cms_foo_admin_foo_edit';
	protected $route_show = 'cms_foo_admin_foo_show';
	protected $route_delete = 'cms_foo_admin_foo_delete';
	protected $route_publish = 'cms_foo_admin_foo_publish_toggle';
	protected $route_group_process = 'cms_foo_admin_foo_group_process';
	//Default values
	protected $template_index = 'CmsAdminBundle:CRUD:index.html.twig';
	protected $template_new = 'CmsAdminBundle:CRUD:new.html.twig';
	protected $template_edit = 'CmsAdminBundle:CRUD:edit.html.twig';
	protected $template_show = 'CmsAdminBundle:CRUD:show.html.twig';
	protected $template_menuleft = 'CmsAdminBundle::menuleft.html.twig';
	protected $max_per_page = 10;

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->buildController();
	}

	public function buildController() {

		if (false !== $pos = strpos($this->doctrine_namespace, ':')) {
			$this->bundle_name = substr($this->doctrine_namespace, 0, $pos);
			$class_name = substr($this->doctrine_namespace, $pos + 1);
			$class_path = $this->getDoctrine()->getAliasNamespace($this->bundle_name);
			if (!class_exists($this->entity_name))
				$this->entity_name = $class_path . '\\' . $class_name;

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

			$group_object_name = $class_path . 'Form\\Model\\' . $class_name . 'Group';
			if (class_exists($group_object_name))
				$this->group_object_name = $group_object_name;

			$group_form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'GroupFormType';
			if (class_exists($group_form_type_name))
				$this->group_form_type_name = $group_form_type_name;

			if (!class_exists($this->group_form_handler_name))
				$this->group_form_handler_name = $class_path . 'Form\\Handler\\' . $class_name . 'GroupFormHandler';

			$this->translation_prefix = $this->container->underscore($class_name);
			if (empty($this->route_prefix)) {
				$this->route_prefix = $this->container->underscore(preg_replace('#Bundle$#', '', $this->bundle_name)) . '_admin';
			}
			$this->route_index = ($this->route_index != 'cms_foo_admin_foo_index') ? $this->route_index : $this->route_prefix . '_' . $this->translation_prefix . '_index';
			$this->route_new = ($this->route_new != 'cms_foo_admin_foo_new') ? $this->route_new : $this->route_prefix . '_' . $this->translation_prefix . '_new';
			$this->route_edit = ($this->route_edit != 'cms_foo_admin_foo_edit') ? $this->route_new : $this->route_prefix . '_' . $this->translation_prefix . '_edit';
			$this->route_show = ($this->route_show != 'cms_foo_admin_foo_show') ? $this->route_new : $this->route_prefix . '_' . $this->translation_prefix . '_show';
			$this->route_delete = ($this->route_delete != 'cms_foo_admin_foo_delete') ? $this->route_new : $this->route_prefix . '_' . $this->translation_prefix . '_delete';
			$this->route_publish = ($this->route_publish != 'cms_foo_admin_foo_publish_toggle') ? $this->route_new : $this->route_prefix . '_' . $this->translation_prefix . '_publish_toggle';
			$this->route_group_process = ($this->route_group_process != 'cms_foo_admin_foo_group_process') ? $this->route_group_process : $this->route_prefix . '_' . $this->translation_prefix . '_group_process';

			$template_index = $this->bundle_name . ':Admin' . $class_name . ':index.html.twig';
			if ($this->get('templating')->exists($template_index)) {
				$this->template_index = $template_index;
			}
			$template_new = $this->bundle_name . ':Admin' . $class_name . ':new.html.twig';
			if ($this->get('templating')->exists($template_new)) {
				$this->template_new = $template_new;
			}
			$template_edit = $this->bundle_name . ':Admin' . $class_name . ':edit.html.twig';
			if ($this->get('templating')->exists($template_edit)) {
				$this->template_edit = $template_edit;
			}
			$template_show = $this->bundle_name . ':Admin' . $class_name . ':show.html.twig';
			if ($this->get('templating')->exists($template_show)) {
				$this->template_show = $template_show;
			}
			$template_menuleft = $this->bundle_name . ':Admin' . $class_name . ':menuleft.html.twig';
			if ($this->get('templating')->exists($template_menuleft)) {
				$this->template_menuleft = $template_menuleft;
			}
		}
	}

	protected function getClassRepository() {
		return $this->getDoctrine()->getRepository($this->entity_name);
	}

	protected function getGroupForm($entity) {
		return $this->createForm(new $this->group_form_type_name(), $entity, array(
					'data_class' => $this->group_object_name,
					'translation_domain' => $this->translation_domain
		));
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

	protected function redirectEditSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
	}

	protected function redirectNewSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
	}

	protected function redirectPublishSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectDeleteSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectGroupProcessSuccess($process_action) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectGroupProcessError($process_action) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function getTemplateFor($action, $modal) {
		$template_name = $this->{'template_' . $action};
		$template_prefix = ($this->getRequest()->isXmlHttpRequest() || $modal) ? 'ajax_' : '';
		if (!empty($template_prefix)) {
			list($bundle_name, $module, $template) = explode(':', $template_name);
			$template_name = $bundle_name . ':' . $module . ':' . $template_prefix . $template;
		}
		return $template_name;
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
					'route_form_action' => $this->route_group_process,
					'template_menuleft' => $this->template_menuleft
		));
	}

	public function processFilter($filter, $filter_entity) {

		if ('POST' == $this->getRequest()->getMethod()) {
			$handler = new $this->filter_form_handler_name(
					$this->getRequest(), $this->getDoctrine()->getManager()
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

	public function newAction($modal = false) {
		$entity = new $this->entity_name();

		$form = $this->getForm($entity);

		$handler = new $this->form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getManager()
		);

		if ($handler->process($form, $this)) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.new', array('%name%' => $entity), $this->bundle_name)
			);

			if ($this->getRequest()->get('save_and_add') != null) {
				return $this->redirect($this->generateUrl($this->route_new));
			}
			return $this->redirectNewSuccess($entity);
		}

		return $this->render($this->getTemplateFor('new', $modal), array(
					'modal' => $modal,
					'modal_id' => 'modal_' . $this->translation_prefix . '_new',
					'form' => $form->createView(),
					'route_form_action' => $this->route_new,
					'route_index' => $this->route_index,
					'translation_prefix' => $this->translation_prefix,
					'bundle_name' => $this->bundle_name,
					'template_menuleft' => $this->template_menuleft
		));
	}

	public function editAction($id, $modal = false) {
		$entity = $this->retrieveEntity($id);

		$form = $this->getForm($entity);

		$handler = new $this->form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getManager()
		);

		if ($handler->process($form, $this)) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.edit', array('%name%' => $entity), $this->bundle_name)
			);

			return $this->redirectEditSuccess($entity);
		}

		return $this->render($this->getTemplateFor('edit', $modal), array(
					'modal' => $modal,
					'modal_id' => 'modal_' . $this->translation_prefix . '_edit_' . $id,
					'form' => $form->createView(),
					'entity' => $entity,
					'route_form_action' => $this->route_edit,
					'route_index' => $this->route_index,
					'route_delete' => $this->route_delete,
					'route_show' => $this->route_show,
					'translation_prefix' => $this->translation_prefix,
					'bundle_name' => $this->bundle_name,
					'template_menuleft' => $this->template_menuleft
		));
	}

	public function deleteAction($id) {
		$entity = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($entity);
		$em->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.delete', array(), $this->bundle_name)
		);

		return $this->redirectDeleteSuccess($entity);
	}

	public function groupProcessAction() {
		$form = $this->getGroupForm(new $this->group_object_name());

		$handler = new $this->group_form_handler_name(
				$this->getRequest(), $this->getDoctrine()->getManager()
		);

		try {
			$process = $handler->process($form, $this->getRequest()->get('ids'));
		} catch (NotValidException $e) {

			$this->get('session')->setFlash('error', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.error.group.' . $process, array(), $this->bundle_name
					)
			);
			return $this->redirectGroupProcessError($process);
		}

		if ($process != false) {
			$this->get('session')->setFlash('success', $this->get('translator')->trans(
							$this->translation_prefix . '.flash.success.group.' . $process, array(), $this->bundle_name
					)
			);
		}

		return $this->redirectGroupProcessSuccess($process);
	}

	public function publishState($id, $publish_state) {

		$em = $this->getDoctrine()->getManager();

		$entity = $this->getClassRepository()->findOneById($id);

		//toggle
		if ($publish_state === null) {
			$publish_state = ($entity->getPublished()) ? false : true;
		}

		$entity->setPublished($publish_state);

		$em->persist($entity)->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.' . ($entity->getPublished() ? '' : 'un') . 'publish', array(), $this->bundle_name)
		);

		return $this->redirectPublishSuccess($entity);
	}

	public function publishToggleAction($id) {
		return $this->publishState($id, null);
	}

	public function publishAction($id) {
		return $this->publishState($id, true);
	}

	public function unpublishAction($id) {
		return $this->publishState($id, false);
	}

}
