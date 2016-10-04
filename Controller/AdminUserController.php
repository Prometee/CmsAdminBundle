<?php

namespace Cms\Bundle\AdminBundle\Controller;

use Doctrine\ORM\EntityManager;

class AdminUserController extends BaseAdminController {

	public $doctrine_namespace = "CmsAdminBundle:User";
    protected $route_prefix = 'cms_admin_admin';
    protected $translation_domain = 'CmsAdminBundle';
    protected $form_type_name = 'Cms\\Bundle\\AdminBundle\\Form\\Type\\UserFormType';

    protected function buildController()
    {
        //Try to found the class configured for the current project
        $class = $this->getParameter('fos_user.model.user.class');
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $this->doctrine_namespace = $em->getClassMetadata($class)->getName();
        parent::buildController();
    }
}
