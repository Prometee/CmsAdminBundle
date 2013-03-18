<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BaseAdminGroupFormType extends AbstractType {
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('action');
    }

    public function getName() {
        return 'core_admin_bundle_group';
    }
}