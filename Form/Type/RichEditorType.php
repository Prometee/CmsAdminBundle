<?php

namespace Cms\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RichEditorType extends TextareaType {

	/**
	 * {@inheritdoc}
	 */
	public function getParent() {
		return TextareaType::class;
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
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'required' => 0,
            'attr' => array(
                'class'=>'tinymce',
                'data-theme'=>'simple'
            )
        ));
    }

}
