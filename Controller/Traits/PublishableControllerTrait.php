<?php

namespace Cms\Bundle\AdminBundle\Controller\Traits;

trait PublishableControllerTrait {

	protected $route_publish = 'cms_foo_admin_foo_publish_toggle';
	protected $publish_group_object_name = 'Cms\\Bundle\\AdminBundle\\Form\\Model\\PublishableAdminGroup';

    protected function preConfigureTrait() {
        $this->available_route_names[] = 'publish';
        $this->addDefaultRenderParameter('route_publish');
    }

    protected function postConfigureTrait() {
        $this->group_object_name = $this->publish_group_object_name;
    }

	protected function redirectPublishSuccess($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function redirectPublishError($entity = null) {
		return $this->redirect($this->generateUrl($this->route_index));
	}

	protected function publishState($id, $publish_state) {

		$em = $this->getDoctrine()->getManager();

		$entity = $this->getClassRepository()->findOneById($id);

		//toggle
		if ($publish_state === null) {
			$publish_state = ($entity->getPublished()) ? false : true;
		}

		$entity->setPublished($publish_state);

		try {
			$em->persist($entity);
                        $em->flush();
		} catch (\Exception $e) {
			$this->get('session')->getFlashBag()->set('error', $this->get('translator')->trans(
				$this->translation_prefix . '.flash.error.' . ($entity->getPublished() ? '' : 'un') . 'publish',
				array('%exception%'=>$e->getMessage()),
				$this->bundle_name
			));

			return $this->redirectPublishError();
		}

		$this->get('session')->getFlashBag()->set('success', $this->get('translator')->trans(
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

?>
