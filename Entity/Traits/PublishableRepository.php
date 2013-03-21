<?php

namespace Cms\Bundle\AdminBundle\Entity\Traits;

/**
 * Timestampable Trait, usable with PHP >= 5.4
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Gedmo.Timestampable.Traits
 * @subpackage TimestampableEntity
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
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
