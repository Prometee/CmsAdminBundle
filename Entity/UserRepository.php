<?php

namespace Cms\Bundle\AdminBundle\Entity;

use Cms\Bundle\AdminBundle\Entity\Traits\BaseAdminEntityRepository;
use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository {
	
	use BaseAdminEntityRepository;
}
