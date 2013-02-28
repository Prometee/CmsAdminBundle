<?php

namespace Cms\Bundle\AdminBundle\Form\Extension\Type;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RichEditorType extends TextareaType {

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options) {
		$parent_options = parent::getDefaultOptions($options);
		$parent_options['required'] = 0;
		$parent_options['attr']['class'] = 'tinymce';
		$parent_options['attr']['data-theme'] = 'simple';
		return $parent_options;
	}

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

}