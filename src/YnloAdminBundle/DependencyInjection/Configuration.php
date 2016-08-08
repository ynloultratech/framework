<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ynlo_admin')->addDefaultsIfNotSet()->children();

        $rootNode->enumNode('skin')->values(
            [
                'blue',
                'black',
                'purple',
                'green',
                'red',
                'yellow',
                'blue-light',
                'black-light',
                'purple-light',
                'green-light',
                'red-light',
                'yellow-light',
            ]
        )->defaultValue('blue');

        $rootNode->enumNode('mode')->values(['fixed', 'boxed'])->defaultValue('fixed');
        $rootNode->booleanNode('sidebar_mini')->defaultValue(true)->info('Show a little sidebar bar when the sidebar is collapsed, otherwise the sidebar is hidden.');
        $rootNode->booleanNode('collapsed_sidebar')->defaultValue(false)->info('Have a collapsed sidebar upon loading.');

        $rootNode->scalarNode('icheck')->defaultValue('flat-blue')->info('Set the theme to use or false to disable');

        return $treeBuilder;
    }
}
