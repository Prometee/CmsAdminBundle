<?php

namespace Cms\Bundle\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionTypeExtension extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType() {
		return CollectionType::class;
	}

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver  $resolver) {
        $resolver->addAllowedTypes('modal', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['modal'] = isset($options['modal']) && $options['modal'] ? true : false;
        $view->vars['prototype_name'] = $options['prototype_name'];
    }
}
