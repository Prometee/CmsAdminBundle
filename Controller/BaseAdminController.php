<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Cms\Bundle\AdminBundle\Controller\Exception\MissingDoctrineNamespaceException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseAdminController extends Controller
{

    //This first attribute must be implemented by the master class
    protected $doctrine_namespace = "CmsAdminBundle:Foo";

    protected $translation_prefix = null; //'foo'
    protected $translation_domain = null; //'CmsAdminBundle'
    protected $bundle_name = 'CmsAdminBundle';
    protected $entity_name = 'Foo';

    protected $form_type_name = 'FooFormType';

    protected $filter_object_name = 'FooFilter';
    protected $filter_form_type_name = 'FooFilterFormType';

    protected $group_object_name = 'Cms\Bundle\AdminBundle\Form\Model\BaseAdminGroup';
    protected $group_form_type_name = 'Cms\Bundle\AdminBundle\Form\Type\BaseAdminGroupFormType';

    protected $route_prefix = '';
    protected $route_index = 'cms_foo_admin_foo_index';
    protected $route_new = 'cms_foo_admin_foo_new';
    protected $route_create = 'cms_foo_admin_foo_create';
    protected $route_edit = 'cms_foo_admin_foo_edit';
    protected $route_update = 'cms_foo_admin_foo_update';
    protected $route_show = 'cms_foo_admin_foo_show';
    protected $route_delete = 'cms_foo_admin_foo_delete';
    protected $route_group_process = 'cms_foo_admin_foo_group_process';

    protected $available_route_names = array(
        'index',
        'new',
        'create',
        'edit',
        'update',
        'show',
        'delete',
        'group_process'
    );

    protected $custom_global_template_folder = '';

    protected $template_index = 'CmsAdminBundle:CRUD:index.html.twig';
    protected $template_new = 'CmsAdminBundle:CRUD:new.html.twig';
    protected $template_edit = 'CmsAdminBundle:CRUD:edit.html.twig';
    protected $template_show = 'CmsAdminBundle:CRUD:show.html.twig';
    protected $template_menuleft = 'CmsAdminBundle::menuleft.html.twig';

    protected $template_ajax_new = 'CmsAdminBundle:CRUD:ajax_new.html.twig';
    protected $template_ajax_edit = 'CmsAdminBundle:CRUD:ajax_edit.html.twig';

    protected $available_template_names = array(
        'index',
        'new',
        'edit',
        'show',
        'menuleft',
        'ajax_new',
        'ajax_edit'
    );

    protected $max_per_page = 10;

    protected $default_render_parameters = array(
        'translation_prefix',
        'translation_domain',
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

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->buildController();
    }

    protected function preConfigureTrait() {
        //Use this method for your trait configuration
    }

    protected function preConfigure() { }

    protected function postConfigureTrait() {
        //Use this method for your trait configuration
    }

    protected function postConfigure() { }

    protected function buildController()
    {
        if (false !== $pos = strpos($this->doctrine_namespace, ':')) {

            $this->preConfigureTrait();
            $this->preConfigure();

            $this->bundle_name = substr($this->doctrine_namespace, 0, $pos);
            $class_name = substr($this->doctrine_namespace, $pos + 1);
            $class_path = $this->getDoctrine()->getAliasNamespace($this->bundle_name);
            if (!class_exists($this->entity_name))
                $this->entity_name = $class_path . '\\' . $class_name;

            $class_path = preg_replace('#Entity$#', '', $class_path);
            if (!class_exists($this->form_type_name))
                $this->form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'FormType';

            if (!class_exists($this->filter_object_name))
                $this->filter_object_name = $class_path . 'Form\\Model\\' . $class_name . 'Filter';
            if (!class_exists($this->filter_form_type_name))
                $this->filter_form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'FilterFormType';

            $group_object_name = $class_path . 'Form\\Model\\' . $class_name . 'Group';
            if (class_exists($group_object_name))
                $this->group_object_name = $group_object_name;

            $group_form_type_name = $class_path . 'Form\\Type\\' . $class_name . 'GroupFormType';
            if (class_exists($group_form_type_name))
                $this->group_form_type_name = $group_form_type_name;

            if (!$this->translation_prefix) {
                $this->translation_prefix = $this->container->underscore($class_name);
            }
            if (!$this->translation_domain) {
                $this->translation_domain = $this->bundle_name;
            }
            if (empty($this->route_prefix)) {
                $this->route_prefix = $this->container->underscore(preg_replace('#Bundle$#', '', $this->bundle_name)) . '_admin';
            }

            //Check if child class don't override any $this->route_*
            foreach ($this->available_route_names as $name) {
                $this->{'route_' . $name} = ($this->{'route_' . $name} != 'cms_foo_admin_foo_'.$name)
                    ? $this->{'route_' . $name}
                    : $this->route_prefix . '_' . $this->translation_prefix . '_' . $name;
            }
            foreach ($this->available_template_names as $name) {
                $template = $this->bundle_name . ':Admin' . $class_name . ':' . $name . '.html.twig';
                $global_template = $this->bundle_name . ':' . $this->custom_global_template_folder . ':' . $name . '.html.twig';
                if ($this->get('templating')->exists($template)) {
                    //If a template is available into the current controller view
                    $this->{'template_' . $name} = $template;
                } else if ($this->get('templating')->exists($global_template)) {
                    //If a template is available into parent folder
                    //of the current controller view
                    $this->{'template_' . $name} = $global_template;
                }
            }

            $this->postConfigureTrait();
            $this->postConfigure();

        } else {
            throw new MissingDoctrineNamespaceException('Please provide $this->doctrine_namespace.');
        }
    }

    protected function addDefaultRenderParameter($param)
    {
        if (!in_array($param, $this->default_render_parameters)) {
            $this->default_render_parameters[] = $param;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        foreach ($this->default_render_parameters as $parameter) {
            if (!isset($parameters[$parameter])) {
                $parameters[$parameter] = $this->$parameter;
            }
        }
        return parent::render($view, $parameters, $response);
    }

    protected function getTemplateFor($action, $modal) {
        return $this->{'template_'. ($this->getRequest()->isXmlHttpRequest() || $modal ? 'ajax_' : '') . $action};
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getClassRepository() {
        return $this->getDoctrine()->getRepository($this->entity_name);
    }

    protected function getGroupForm($entity, $form_options = array())
    {
        $form_options = array_unique(array_merge(array(
                'data_class' => $this->group_object_name,
                'ids_class' => $this->doctrine_namespace,
                'translation_domain' => 'CmsAdminBundle'
            ),
            $form_options
        ));
        return $this->createForm(new $this->group_form_type_name(), $entity, $form_options);
    }

    protected function getForm($entity, $form_options = array()) {
        return $this->createForm(new $this->form_type_name(), $entity, $form_options);
    }

    protected function getFilterForm($entity)
    {
        if (class_exists($this->filter_form_type_name)) {
            return $this->createForm(new $this->filter_form_type_name(), $entity);
        } else {
            return null;
        }
    }

    protected function retrieveEntity($id)
    {
        return $this->getClassRepository()->find($id);
    }

    protected function retrieveEntityList()
    {
        return $this->getClassRepository()->findAll();
    }

    protected function redirectEditSuccess($entity = null)
    {
        return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
    }

    protected function redirectNewSuccess($entity = null)
    {
        return $this->redirect($this->generateUrl($this->route_edit, array('id' => $entity->getId())));
    }

    protected function redirectDeleteSuccess($entity = null)
    {
        return $this->redirect($this->generateUrl($this->route_index));
    }

    protected function redirectDeleteError($entity = null)
    {
        return $this->redirect($this->generateUrl($this->route_index));
    }

    protected function redirectGroupProcessSuccess()
    {
        return $this->redirect($this->generateUrl($this->route_index));
    }

    public function indexAction(Request $request)
    {
        $entity = new $this->group_object_name();

        $form = $this->createGroupForm($entity);

        //For errors in group process
        $form->handleRequest($request);

        $filter_entity = (class_exists($this->filter_object_name)) ? new $this->filter_object_name() : null;
        $filter = $this->getFilterForm($filter_entity);

        $query = $this->retrieveEntityList();

        $pagination = $this->get('knp_paginator')
            ->paginate($query, $request->query->get('page', 1), $this->max_per_page);

        return $this->render($this->template_index, array(
            'filter' => (($filter) ? $filter->createView() : null),
            'pagination' => $pagination,
            'groupForm' => $form->createView()
        ));
    }

    public function showAction($id)
    {
        $entity = $this->getClassRepository()->findOneBy(array('id' => $id));

        if (!$entity || !$this->checkEntityRole($entity)) {
            throw $this->createNotFoundException($this->get('translator')->trans($this->translation_prefix . '.404', array(), $this->translation_domain));
        }

        return $this->render($this->template_show, array(
            'entity' => $entity
        ));
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    public function createNewForm($entity, $modal = false) {
        $form = $this->getForm($entity, array(
            'action' => $this->generateUrl($this->route_create)
        ));
        $form->add('submit', 'submit');
        if (!$modal) {
            $form->add('save_and_add', 'submit');
        }

        return $form;
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    protected function createEditForm($entity) {
        $form = $this->getForm($entity, array(
            'action' => $this->generateUrl($this->route_update, array('id' => $entity->getId())),
            'method' => 'PUT'
        ));

        $form->add('submit', 'submit');

        return $form;
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Form\Form
     */
    protected function createGroupForm($entity)
    {
        $form = $this->getGroupForm($entity, array(
            'action' => $this->generateUrl($this->route_group_process),
            'method' => 'POST'
        ));

        $form->add('submit', 'submit');

        return $form;
    }

    /**
     * @param mixed $entity
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($this->route_delete, array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit')
            ->getForm();
    }

    public function newAction($modal = false)
    {
        $entity = new $this->entity_name();

        $form = $this->createNewForm($entity, $modal);

        return $this->render($this->getTemplateFor('new', $modal), array(
            'modal' => $modal,
            'modal_id' => 'modal_' . $this->translation_prefix . '_new',
            'form' => $form->createView(),
            'entity' => $entity
        ));
    }

    public function createAction(Request $request, $modal = false)
    {
        $entity = new $this->entity_name();

        $form = $this->createNewForm($entity, $modal);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                $this->translation_prefix . '.flash.success.new', array('%name%' => $entity), $this->translation_domain
            ));
            if (!$modal && $form->get('save_and_add')->isClicked()) {
                return $this->redirect($this->generateUrl($this->route_new));
            } else {
                return $this->redirectEditSuccess($entity);
            }
        } else {
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.new', array(), $this->translation_domain
                )
            );

            return $this->render($this->getTemplateFor('new', $modal), array(
                'modal' => $modal,
                'modal_id' => 'modal_' . $this->translation_prefix . '_new',
                'form' => $form->createView(),
                'entity' => $entity
            ));
        }
    }

    /**
     * This method allow you to filter access to entity
     *
     * @param $entity
     * @return bool
     */
    protected function checkEntityRole($entity) {
        return $entity ? true : false;
    }

    public function editAction($id, $modal = false)
    {
        $entity = $this->retrieveEntity($id);

        if (!$entity || !$this->checkEntityRole($entity)) {
            throw $this->createNotFoundException($this->get('translator')->trans($this->translation_prefix . '.404', array(), $this->translation_domain));
        }

        $form = $this->createEditForm($entity);
        $form_delete = $this->createDeleteForm($entity);

        return $this->render($this->getTemplateFor('edit', $modal), array(
            'modal' => $modal,
            'modal_id' => 'modal_' . $this->translation_prefix . '_edit_' . $id,
            'form' => $form->createView(),
            'form_delete' => $form_delete->createView(),
            'entity' => $entity
        ));
    }

    public function updateAction(Request $request, $id, $modal = false)
    {
        $entity = $this->retrieveEntity($id);

        if (!$entity || !$this->checkEntityRole($entity)) {
            throw $this->createNotFoundException($this->get('translator')->trans($this->translation_prefix . '.404', array(), $this->translation_domain));
        }

        $form = $this->createEditForm($entity);
        $form_delete = $this->createDeleteForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.success.edit', array('%name%' => $entity), $this->translation_domain)
            );

            return $this->redirectEditSuccess($entity);
        } else {
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.edit', array('%name%' => $entity), $this->translation_domain
                )
            );

            return $this->render($this->getTemplateFor('edit', $modal), array(
                'modal' => $modal,
                'modal_id' => 'modal_' . $this->translation_prefix . '_edit_' . $id,
                'form' => $form->createView(),
                'form_delete' => $form_delete->createView(),
                'entity' => $entity
            ));
        }
    }

    public function deleteAction(Request $request, $id)
    {
        $entity = $this->retrieveEntity($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans($this->translation_prefix . '.404', array(), $this->translation_domain));
        }

        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.success.delete', array(), $this->translation_domain)
            );
        } else {
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                $this->translation_prefix . '.flash.error.delete', array(), $this->translation_domain
            ));

            return $this->redirectDeleteError($entity);
        }

        return $this->redirectDeleteSuccess($entity);
    }

    public function groupProcessAction(Request $request)
    {
        $entity = new $this->group_object_name();

        $form = $this->createGroupForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $this->groupProcess($data->action, $data->ids);

            $this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
                $this->translation_prefix . '.flash.success.group.' . $data->action, array(), $this->translation_domain
            ));
        } else {
            $this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
                    $this->translation_prefix . '.flash.error.group.' . $form->getData()->action, array(), $this->translation_domain
                )
            );
            return $this->indexAction($request);
        }

        return $this->redirectGroupProcessSuccess();
    }

    public function groupProcess($action, $entity_list) {
        $ids = array();
        foreach($entity_list as $entity) {
            $ids[] = $entity->getId();
        }
        $repository = $this->getClassRepository();
        if (method_exists($repository, $action . 'Group')) {
            $repository->{$action . 'Group'}($ids);
        } else {
            throw new \BadMethodCallException('The method ' . get_class($repository) . '::' . $action . 'Group doesn\'t exist please create it !');
        }
    }
}
