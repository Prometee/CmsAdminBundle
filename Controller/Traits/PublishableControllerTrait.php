<?php

namespace Cms\Bundle\AdminBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Request;

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

	protected function publishState(Request $request, $id, $publish_state) {

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
			$request->getSession()->getFlashBag()->set('error', $this->get('translator')->trans(
				$this->translation_prefix . '.flash.error.' . ($entity->getPublished() ? '' : 'un') . 'publish',
				array('%exception%'=>$e->getMessage()),
				$this->bundle_name
			));

			return $this->redirectPublishError();
		}

		$request->getSession()->getFlashBag()->set('success', $this->get('translator')->trans(
			$this->translation_prefix . '.flash.success.' . ($entity->getPublished() ? '' : 'un') . 'publish', array(), $this->bundle_name)
		);

		return $this->redirectPublishSuccess($entity);
	}

	public function publishToggleAction(Request $request, $id) {
		return $this->publishState($request, $id, null);
	}

	public function publishAction(Request $request, $id) {
		return $this->publishState($request, $id, true);
	}

	public function unpublishAction(Request $request, $id) {
		return $this->publishState($request, $id, false);
	}
}

?>
