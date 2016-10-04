<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username')
                ->add('email')
                ->add('plainPassword', null, array('required' => $options['data']->getId() ? false : true))
                ->add('enabled', null, array('required' => false))
                ->add('roles', ChoiceType::class, array(
                    'choices' => call_user_func(array($options['data_class'], 'getRolesChoices')),
                    'multiple' => true
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\Bundle\AdminBundle\Entity\User',
            'translation_domain' => 'CmsAdminBundle'
        ));
    }

    public function getName() {
        return 'cms_admin_user';
    }

}

