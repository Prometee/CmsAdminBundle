<?php

namespace Cms\Bundle\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('cms_admin')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('templating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('bootstrap3')->defaultValue("bootstrap_3_layout.html.twig")->end()
                        ->scalarNode('fields')->defaultValue("CmsAdminBundle:Form:fields.html.twig")->end()
                        ->scalarNode('collection')->defaultValue("CmsAdminBundle:Form:collection.html.twig")->end()
                        ->scalarNode('collection_modal')->defaultValue("CmsAdminBundle:Form:collection_modal.html.twig")->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
