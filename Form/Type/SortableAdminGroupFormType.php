<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class SortableAdminGroupFormType extends BaseAdminGroupFormType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('action', HiddenType::class);
        parent::buildForm($builder, $options);
    }

    public function getName() {
        return 'cms_sortable_group';
    }
}
