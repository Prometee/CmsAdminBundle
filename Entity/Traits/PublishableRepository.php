<?php

namespace Cms\Bundle\AdminBundle\Entity\Traits;

trait PublishableRepository {
    
	public function publishGroup($ids)
    {
        foreach($this->findBy(array('id'=>$ids)) as $entity) {
            $entity->setPublished(true);
            $this->_em->persist($entity);
        }
        
        $this->_em->flush();
    }

    public function unpublishGroup($ids)
    {
        foreach($this->findBy(array('id'=>$ids)) as $entity) {
            $entity->setPublished(false);
            $this->_em->persist($entity);
        }
        
        $this->_em->flush();
    }
}
