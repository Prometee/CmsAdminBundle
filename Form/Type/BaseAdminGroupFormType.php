<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BaseAdminGroupFormType extends AbstractType {

    protected $name_prefix;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('action', 'choice', array(
            'choices' => call_user_func(array($options['data_class'], 'getActions'))
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setRequired(array(
            'data_class'
        ));
    }

    public function getName() {
        return 'cms_admin_group';
    }

}