<?php

namespace Cms\Bundle\AdminBundle\Form\Model;

class PublishableAdminGroup extends BaseAdminGroup {

    function __construct() {
        self::$actions['publish'] = 'global.form_action.group.publish';
        self::$actions['unpublish'] = 'global.form_action.group.unpublish';
    }
}