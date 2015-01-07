<?php

namespace Cms\Bundle\AdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AbstractEntityToIntegerTransformer implements DataTransformerInterface {
	
	protected $om;
    protected $repository = 'CmsAdminBundle:Foo';
	
	public function __construct(ObjectManager $om) {
        $this->om = $om;
    }
	
	public function transform($entity) {
		
		$id = null;
		
		if (null !== $entity) {
            $id = $entity->getId();
        }
		
		return (int) $id;
	}
	
	public function reverseTransform($id) {
		
		if (!$id) {
            return null;
        }

        $entity = $this->om
            ->getRepository($this->repository)
            ->findOneById($id)
        ;
        
        if ($entity === null) {
            throw new TransformationFailedException();
        }

        return $entity;
	}
}