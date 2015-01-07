<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RichEditorType extends TextareaType {

	/**
	 * {@inheritdoc}
	 */
	public function getParent() {
		return 'textarea';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'richeditor';
	}

	/**
	 * {@inheritdoc}
	 */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'required' => 0,
            'attr' => array(
                'class'=>'tinymce',
                'data-theme'=>'simple'
            )
        ));
    }

}
