<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminOrderGroupFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('action', 'hidden', array(
            'data'   => 'order'
        ));
    }

    public function getName()
    {
        return 'cms_order_group';
    }
}