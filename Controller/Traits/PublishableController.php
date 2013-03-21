<?php

namespace Cms\Bundle\AdminBundle\Controller\Traits;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Exception\NotValidException;

trait PublishableController {
	
	protected $route_publish = 'cms_foo_admin_foo_publish_toggle';

	protected function redirectPublishSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	public function publishState($id, $publish_state) {

		$em = $this->getDoctrine()->getManager();

		$entity = $this->getClassRepository()->findOneById($id);

		//toggle
		if ($publish_state === null) {
			$publish_state = ($entity->getPublished()) ? false : true;
		}

		$entity->setPublished($publish_state);

		$em->persist($entity)->flush();

		$this->get('session')->setFlash('success', $this->get('translator')->trans(
						$this->translation_prefix . '.flash.success.' . ($entity->getPublished() ? '' : 'un') . 'publish', array(), $this->bundle_name)
		);

		return $this->redirectPublishSuccess($entity);
	}

	public function publishToggleAction($id) {
		return $this->publishState($id, null);
	}

	public function publishAction($id) {
		return $this->publishState($id, true);
	}

	public function unpublishAction($id) {
		return $this->publishState($id, false);
	}

}
