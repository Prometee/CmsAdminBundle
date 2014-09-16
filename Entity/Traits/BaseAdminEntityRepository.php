<?php

namespace Cms\Bundle\AdminBundle\Entity\Traits;

use Doctrine\ORM\Query;

trait BaseAdminEntityRepository {
	
	public function deleteGroup($ids) {
        $qb = $this->createQueryBuilder('t');

        $qb->delete()
			->where($qb->expr()->in('t.id', $ids))
			->getQuery()
			->execute(null, Query::HYDRATE_OBJECT);

        $this->_em->flush();
    }
}