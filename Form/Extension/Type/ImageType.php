<?php

namespace Cms\Bundle\AdminBundle\Form\Extension\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ImageType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->setAttribute('image_width', $options['image_width']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		$view->set('image_width', $form->getAttribute('image_width'));
	}

	public function getParent() {
		return 'field';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'image';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultOptions(array $options) {
		return array(
			'image_width' => 200,
		);
	}

}