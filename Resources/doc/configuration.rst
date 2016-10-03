*********
Configure
*********

**Table of Contents**

.. contents::
    :local:
    :depth: 2

==========================
The app/AppKernel.php file
==========================

Check if the all this bundles are loaded :

.. code-block:: php

    new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
    new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
    new FOS\UserBundle\FOSUserBundle(),
    new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),

==============================
The app/config/config.yml file
==============================

Add the following line in the import section (top of the *app/config/config.yml* file)

.. code-block:: yaml

    imports:
        - { resource: "@CmsAdminBundle/Resources/config/default_config/config.yml" }

If you want to use your entity add this lines to your *app/config/config.yml* :

.. code-block:: yaml

    fos_user:
        db_driver: orm
        firewall_name: main
        user_class: Acme\Bundle\MyBundle\Entity\User

================================
The app/config/security.yml file
================================

Replace all the content of this file by this :

.. code-block:: yaml

    security:

        providers:
            fos_userbundle:
                id: fos_user.user_provider.username

        firewalls:
            # disables authentication for assets and the profiler, adapt it according to your needs
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false

            main:
                pattern: ^/
                form_login:
                    provider: fos_userbundle
                    csrf_token_generator: security.csrf.token_manager

                logout:       true
                anonymous:    true

        access_control:
            - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/, role: ROLE_ADMIN }

================================
The app/config/routing.yml file
================================

Replace all the content of this file by this :

.. code-block:: yaml

    fos_user:
        resource: "@FOSUserBundle/Resources/config/routing/all.xml"

    cms_admin:
        resource: "@CmsAdminBundle/Resources/config/routing/admin_all.yml"
        prefix: "/admin"

=========================
The app/Resources folder
=========================

Remove the content of this folder

Next, in order to get FosUserBundle well display with bootstrap you need to link (or copy) the folder :
*vendor/prometee/cms-bundle/Cms/Bundle/AdminBundle/Resources/FOSUserBundle*
in *app/Resources/*

.. code-block:: bash

    cd app/Resources
    ln -s ../../vendor/prometee/cms-bundle/Resources/FOSUserBundle .