<?php

namespace Cms\Bundle\AdminBundle\Entity\Traits;

trait BaseAdminEntityRepository {
	
	public function deleteGroup($ids) {
        $qb = $this->createQueryBuilder('t');

        $qb->delete()
			->where($qb->expr()->in('t.id', $ids))
			->getQuery()
			->execute();

        $this->_em->flush();
    }
}