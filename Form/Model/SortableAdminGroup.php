<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

class SortableAdminGroup extends BaseAdminGroup {

    public function __construct() {
        self::$actions = array(
            'order'=>''
        );
        $this->action = 'order';
    }
}
