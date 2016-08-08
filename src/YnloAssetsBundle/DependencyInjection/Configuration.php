<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
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
        $rootNode = $treeBuilder->root('ynlo_assets')->children();

        $rootNode->booleanNode('requirejs')->defaultTrue()->info('Use RequireJs to load javascript modules.');
        $rootNode->booleanNode('cdn')->defaultFalse()->info('Use cdn version of javascript modules.');

        $rootNode
            ->arrayNode('assets')
            ->useAttributeAsKey('id')
            ->example(['jquery' => 'path/to/jquery.js', 'jquery_form' => 'path/to/jquery_form.js'])
            ->prototype('scalar')
            ->info('Array of assets to compile, can be .js or .css files');

        /** @var NodeBuilder $contexts */
        $contexts = $rootNode
            ->arrayNode('contexts')
            ->useAttributeAsKey('id')
            ->example(['app' => ['include' => ['all'], 'exclude' => ['bundle_ynlo_admin']]])
            ->prototype('array')
            ->children();

        $contexts->variableNode('include')->info('Array of assets to include, only this assets will be used.')->end();
        $contexts->variableNode('exclude')->info('Array of assets to exclude.')->end();
        $contexts->arrayNode('override')->useAttributeAsKey('id')->prototype('scalar')->info('Array of named assets to override')->end();
        $contexts->end();

        /** @var NodeBuilder $jqueryModules */
        $jqueryModules = $rootNode
            ->arrayNode('modules')
            ->useAttributeAsKey('id')
            ->example(['bootstrap' => ['asset' => 'js/bootstrap.min.js', 'deps' => ['jquery']]])
            ->info('Modules are javascript assets always available using RequireJs')
            ->prototype('array')
            ->children();

        $jqueryModules->scalarNode('asset')->end();
        $jqueryModules->scalarNode('cdn')->end();
        $jqueryModules->variableNode('deps')->info('See RequireJs documentation for more information.')->end();
        $jqueryModules->variableNode('exports')->end();
        $jqueryModules->variableNode('init')->end();
        $jqueryModules->variableNode('jquery_plugins')->info('Use this to create a autoloader for jQuery plugins. When one of the functions specified on this config is used a require is done automatically. (NOTE: only works with jQuery plugins and not always)')->example(['datetimepicker'])->end();
        $jqueryModules->end();

        return $treeBuilder;
    }
}
