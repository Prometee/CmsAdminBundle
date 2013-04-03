<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Exception\NotValidException;
use Cms\Bundle\AdminBundle\Controller\Extension\MissingDoctrineNamespaceException;

abstract class BaseAdminController extends Controller {

	//Must to be implemanted by the master class
	protected $doctrine_namespace = "CmsAdminBundle:Foo";
	protected $translation_prefix = 'foo';
	protected $bundle_name = 'CmsAdminBundle';
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
	protected $route_group_process = 'cms_foo_admin_foo_group_process';
	
	protected $template_index = 'FooBundle:AdminFoo:index.html.twig';
	protected $template_new = 'FooBundle:AdminFoo:new.html.twig';
	protected $template_edit = 'FooBundle:AdminFoo:edit.html.twig';
	protected $template_show = 'FooBundle:AdminFoo:show.html.twig';
	protected $template_menuleft = 'FooBundle:AdminFoo:menuleft.html.twig';
	protected $max_per_page = 10;
		
	//Default values
	private $default_template_index = 'CmsAdminBundle:CRUD:index.html.twig';
	private $default_template_new = 'CmsAdminBundle:CRUD:new.html.twig';
	private $default_template_edit = 'CmsAdminBundle:CRUD:edit.html.twig';
	private $default_template_show = 'CmsAdminBundle:CRUD:show.html.twig';
	private $default_template_menuleft = 'CmsAdminBundle::menuleft.html.twig';
	
	protected $default_render_parameters = array(
		'translation_prefix',
		'bundle_name',
		'route_new',
		'route_index',
		'route_edit',
		'route_show',
		'route_delete',
		'template_menuleft',
		'template_index',
		'template_new',
		'template_edit',
		'template_show',
		'template_menuleft'
	);
	
	protected $controller_extension_list = array();
	
	protected $controller_extensions = array();

	public function setContainer(ContainerInterface $container = null) {
		parent::setContainer($container);
		$this->buildController();
	}

	protected function buildController() {

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
			$this->route_edit = ($this->route_edit != 'cms_foo_admin_foo_edit') ? $this->route_edit : $this->route_prefix . '_' . $this->translation_prefix . '_edit';
			$this->route_show = ($this->route_show != 'cms_foo_admin_foo_show') ? $this->route_show : $this->route_prefix . '_' . $this->translation_prefix . '_show';
			$this->route_delete = ($this->route_delete != 'cms_foo_admin_foo_delete') ? $this->route_delete : $this->route_prefix . '_' . $this->translation_prefix . '_delete';
			$this->route_group_process = ($this->route_group_process != 'cms_foo_admin_foo_group_process') ? $this->route_group_process : $this->route_prefix . '_' . $this->translation_prefix . '_group_process';

			if (!$this->get('templating')->exists($this->template_index)) {
				$template_index = $this->bundle_name . ':Admin' . $class_name . ':index.html.twig';
				$this->template_index = $this->get('templating')->exists($template_index) ? $template_index : $this->default_template_index;
			}

			if (!$this->get('templating')->exists($this->template_new)) {
				$template_new = $this->bundle_name . ':Admin' . $class_name . ':new.html.twig';
				$this->template_new = $this->get('templating')->exists($template_new) ? $template_new : $this->default_template_new;
			}

			if (!$this->get('templating')->exists($this->template_edit)) {
				$template_edit = $this->bundle_name . ':Admin' . $class_name . ':edit.html.twig';
				$this->template_edit = $this->get('templating')->exists($template_edit) ? $template_edit : $this->default_template_edit;
			}

			if (!$this->get('templating')->exists($this->template_show)) {
				$template_show = $this->bundle_name . ':Admin' . $class_name . ':show.html.twig';
				$this->template_show = $this->get('templating')->exists($template_show) ? $template_show : $this->default_template_show;
			}

			if (!$this->get('templating')->exists($this->template_menuleft)) {
				$template_menuleft = $this->bundle_name . ':Admin' . $class_name . ':menuleft.html.twig';
				if ($this->get('templating')->exists($template_menuleft)) {
				$this->template_menuleft = $template_menuleft;
				} else {
					$template_menuleft = $this->bundle_name . '::menuleft.html.twig';
					$this->template_menuleft = $this->get('templating')->exists($template_menuleft) ? $template_menuleft : $this->default_template_menuleft;
				}
			}
			
		} else {
			throw new MissingDoctrineNamespaceException('Please provide $this->doctrine_namespace.');
		}
	}
	
	protected function addDefaultRenderParameter($param) {
		if (!in_array($param, $this->default_render_parameters)) {
			$this->default_render_parameters[] = $param;
		}
	}
	public function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null) {
		
		foreach ($this->default_render_parameters as $parameter) {
			if (!in_array($parameter, $parameters)) {
				$parameters[$parameter] = $this->$parameter;
			}
		}
		
		return parent::render($view, $parameters, $response);
	}

	protected function getTemplateFor($action, $modal) {
		$template_name = $this->{'template_' . $action};
		$template_prefix = ($this->getRequest()->isXmlHttpRequest() || $modal) ? 'ajax_' : '';
		if (!empty($template_prefix)) {
			list($bundle_name, $module, $template) = explode(':', $template_name);
			$template_name = $bundle_name . ':' . $module . ':' . $template_prefix . $template;
			if (!$this->get('templating')->exists($template_name)) {
				$template_name = $this->{'default_template_' . $action};
				list($bundle_name, $module, $template) = explode(':', $template_name);
				$template_name = $bundle_name . ':' . $module . ':' . $template_prefix . $template;
			}
		}
		return $template_name;
	}

	protected function getClassRepository() {
		return $this->getDoctrine()->getRepository($this->entity_name);
	}

	protected function getGroupForm($entity) {
		return $this->createForm(new $this->group_form_type_name(), $entity, array(
					'data_class' => $this->group_object_name,
					'translation_domain' => 'CmsAdminBundle'
		));
	}

	protected function getForm($entity) {
		return $this->createForm(new $this->form_type_name(), $entity);
	}

        protected function getFormHandler() {
            return new $this->form_handler_name(
		$this->getRequest(), $this->getDoctrine()->getManager()
            );
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

	protected function redirectDeleteSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectDeleteError($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectGroupProcessSuccess($process_action) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectGroupProcessError($process_action) {
		return $this->redirect($this->generateUrl($this->route_index));
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
					'route_form_action' => $this->route_group_process,
		));
	}

	protected function processFilter($filter, $filter_entity) {

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
					'entity' => $entity
		));
	}

	public function newAction($modal = false) {
		$entity = new $this->entity_name();

		$form = $this->getForm($entity);

		$handler = $this->getFormHandler();

		try {
            $process = $handler->process($form, $this);
        } catch (NotValidException $e) {
            $process = false;
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.new', array(), $this->bundle_name
                )
            );
        }

		if ($process) {
			$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
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
			'entity' => $entity,
			'route_form_action' => $this->route_new,
		));
	}

	public function editAction($id, $modal = false) {
		$entity = $this->retrieveEntity($id);

		$form = $this->getForm($entity);

		$handler = $this->getFormHandler();

		try {
            $process = $handler->process($form, $this);
        } catch (NotValidException $e) {
            $process = false;
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.edit', array('%name%' => $entity), $this->bundle_name
                )
            );
        }

		if ($process) {
			$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
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
		));
	}

	public function deleteAction($id) {
		$entity = $this->getClassRepository()->findOneById($id);
		$em = $this->getDoctrine()->getManager();
		try {
			$em->remove($entity);
			$em->flush();
		} catch (\Exception $e) {
			$this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
				$this->translation_prefix . '.flash.error.delete',
				array('%exception%'=>$e->getMessage()),
				$this->bundle_name
			));
			
			return $this->redirectDeleteError($entity);
		}

		$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
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
			$action = $form->getData()->action;
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.group.' . $action, array(), $this->bundle_name
                )
            );
            return $this->redirectGroupProcessError($action);
        }

        if ($process) {
			$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
				$this->translation_prefix . '.flash.success.group.' . $process, array(), $this->bundle_name
			));
		}

		return $this->redirectGroupProcessSuccess($process);
	}
}
