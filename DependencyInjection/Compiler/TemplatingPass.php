<?php

namespace Cms\Bundle\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder;

class TemplatingPass implements CompilerPassInterface
{
    protected static $templating_configs = array(
        'cms_admin.templating.fields',
        'cms_admin.templating.collection',
        'cms_admin.templating.collection_modal'
    );

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container) {
        foreach(self::$templating_configs as $conf_name) {
            if (false !== ($template = $container->getParameter($conf_name))) {
                $resources = $container->getParameter('twig.form.resources');
                if (!in_array($template, $resources)) {
                    $resources[] = $template;
                    $container->setParameter('twig.form.resources', $resources);
                }
            }
        }
    }
}
