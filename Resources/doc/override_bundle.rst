***************************
Overriding Cms Admin Bundle
***************************

**Table of Contents**

.. contents::
    :local:
    :depth: 2

========================
Clean the default bundle
========================

You can remove all folders under src/ directory but don't forget to delete lines that refer to AcmeDemoBundle for instance in :
 * app/config/routing.yml
 * app/config/routing_dev.yml
 * app/AppKernel.php

===================
The bundle override
===================

Generate the root admin bundle, this bundle will be a child of CmsAdminBundle. This will allow you to override parts of CmsAdminBundle.

*Replace Acme with your nickname or company name*

.. code-block:: bash

    php app/console generate:bundle --namespace=Acme/Bundle/AdminBundle --format=yml

Next your have to edit the root bundle class to add getParent() method like this :
*src/Acme/Bundle/AdminBundle/AcmeAdminBundle.php*

.. code-block:: php

    <?php

    namespace Acme\Bundle\AdminBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AcmeAdminBundle extends Bundle {
        public function getParent() {
            return 'CmsAdminBundle';
        }
    }

