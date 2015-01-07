<?php

namespace Cms\Bundle\AdminBundle\Entity\Traits;

trait SortableRepository {
	
    public function orderGroup($ids) {
		
        foreach($ids as $i=>$id) {
			$entity = $this->find($id);
			if ($entity) {
				$this->_em->persist($entity->setPosition($i));
			}
        }
        
        $this->_em->flush();
    }
}

?>
