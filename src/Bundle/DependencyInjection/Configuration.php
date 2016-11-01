<?php
/**
 * Created by PhpStorm.
 * User: Skilla <sergio.zambrano@gmail.com>
 * Date: 3/10/16
 * Time: 18:55
 */

namespace Skilla\BaseDevelopmentBundle\DependencyInjection;

use \Symfony\Component\Config\Definition\Builder\TreeBuilder;
use \Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('skilla_base_development');

        $rootNode
            ->children()
                ->scalarNode('key')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
