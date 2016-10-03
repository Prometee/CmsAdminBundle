<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseAdminGroupFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if (!$builder->has('action')) {
            $builder->add('action', 'choice', array(
                'choices' => call_user_func(array($options['data_class'], 'getActions'))
            ));
        }
        $builder->add('ids', 'entity', array(
            'multiple'=>true,
            'class'=>$options['ids_class']
        ));
    }

    public function setDefaultOptions(OptionsResolver $resolver) {
        $resolver->setRequired(array(
            'data_class',
            'ids_class',
			'translation_domain'
        ));
    }

    public function getName() {
        return 'cms_admin_group';
    }

}
