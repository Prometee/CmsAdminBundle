*********
Configure
*********

**Table of Contents**

.. contents::
    :local:
    :depth: 2

==============================
The app/config/config.yml file
==============================

Add the following line in the import section (top of the *app/config/config.yml* file)

.. code-block:: yaml

    imports:
        - { resource: ../../vendor/prometee/cms-bundle/Cms/Bundle/AdminBundle/Ressources/config/default_config/config.yml }

If you don't need to modify the User entity you can add this import :

.. code-block:: yaml

    imports:
        - { resource: ../../vendor/prometee/cms-bundle/Cms/Bundle/AdminBundle/Ressources/config/default_config/fos_user.yml }

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
        encoders:
            FOS\UserBundle\Model\UserInterface: sha512

        role_hierarchy:
            ROLE_ADMIN:       ROLE_USER
            ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

        providers:
            fos_userbundle:
                id: fos_user.user_provider.username

        firewalls:
            main:
                pattern: ^/
                form_login:
                    provider: fos_userbundle
                    csrf_provider: form.csrf_provider
                    login_path: fos_user_security_login
                    check_path: fos_user_security_check
                logout:
                    path: fos_user_security_logout
                    target: /
                anonymous:    true

        access_control:
            - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin/, role: ROLE_ADMIN }

================================
The app/config/routing.yml file
================================

Replace all the content of this file by this :

.. code-block:: yaml

    _imagine:
        resource: .
        type:     imagine

    fos_user_security:
        resource: "@FOSUserBundle/Resources/config/routing/security.xml"
        prefix: /admin

    fos_user_profile:
        resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
        prefix: /admin/profile

    fos_user_resetting:
        resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
        prefix: /admin/resetting

    fos_user_change_password:
        resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
        prefix: /admin/profile

    cms_admin_dashboard:
        resource: "@CmsAdminBundle/Resources/config/routing/admin_dashboard.yml"
        prefix: /admin

    cms_admin_user:
        resource: "@HICEFAdminBundle/Resources/config/routing/admin_user.yml"
        prefix: /admin/user

=========================
The app/Ressources folder
=========================

Remove the content of this folder

Next, in order to get FosUserBundle well display with bootstrap you need to link (or copy) the folder :
*vendor/prometee/cms-bundle/Cms/Bundle/AdminBundle/Resources/FOSUserBundle*
in *app/Resources/*

.. code-block:: bash

    cd app/Resources
    ln -s ../../vendor/prometee/cms-bundle/Cms/Bundle/AdminBundle/Resources/FOSUserBundle .