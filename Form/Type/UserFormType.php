<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username')
                ->add('email')
                ->add('plainPassword', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('roles', 'choice', array(
                    'choices' => call_user_func(array($options['data_class'], 'getRolesChoices')),
                    'multiple' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\Bundle\AdminBundle\Entity\User',
            'translation_domain' => 'CmsAdminBundle'
        ));
    }

    public function getName() {
        return 'cms_admin_user';
    }

}

