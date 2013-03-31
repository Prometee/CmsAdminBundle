<?php

namespace Cms\Bundle\AdminBundle\Controller\Extension;

class PublishableControllerExtension extends ControllerExtension {

	public $route_publish = 'cms_foo_admin_foo_publish_toggle';

	public function configure() {
		
		$this->route_publish = ($this->route_publish != 'cms_foo_admin_foo_publish_toggle')
				? $this->route_publish
				: $this->controller->route_prefix . '_' . $this->controller->translation_prefix . '_publish_toggle';
		
		$this->controller->AddDefaultRenderParameter('route_publish');
	}
	
	public function redirectPublishSuccess($entity = null) {
		return $this->controller->redirect($this->controller->generateUrl($this->controller->route_index));
	}
	
	public function redirectPublishError($entity = null) {
		return $this->controller->redirect($this->controller->generateUrl($this->controller->route_index));
	}

	public function publishState($id, $publish_state) {

		$em = $this->controller->getDoctrine()->getManager();

		$entity = $this->controller->getClassRepository()->findOneById($id);

		//toggle
		if ($publish_state === null) {
			$publish_state = ($entity->getPublished()) ? false : true;
		}

		$entity->setPublished($publish_state);

		try {
			$em->persist($entity)->flush();
		} catch (Exception $e) {
			return $this->redirectPublishError();
		}

		$this->controller->get('session')->getFlashBag()->set('success', $this->controller->get('translator')->trans(
			$this->controller->translation_prefix . '.flash.success.' . ($entity->getPublished() ? '' : 'un') . 'publish', array(), $this->controller->bundle_name)
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
