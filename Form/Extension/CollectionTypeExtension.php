<?php

namespace Cms\Bundle\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionTypeExtension extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType() {
		return 'collection';
	}

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setOptional(array(
            'modal'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['modal'] = isset($options['modal']) && $options['modal'] ? true : false;
        $view->vars['prototype_name'] = $options['prototype_name'];
    }
}
