<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

class SortableAdminGroupFormType extends BaseAdminGroupFormType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('action', 'hidden');
        parent::buildForm($builder, $options);
    }

    public function getName() {
        return 'cms_sortable_group';
    }
}