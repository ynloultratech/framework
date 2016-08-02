<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetConfiguration;

class Configuration implements ConfigurationInterface
{
    use AssetConfiguration;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ynlo_framework')->children();

        /* @noinspection UnNecessaryDoubleQuotesInspection */
        $rootNode->scalarNode('debug')->defaultValue('false');
        $pace = $rootNode->arrayNode('pace')
            ->canBeDisabled()
            ->info('Enable or disable the Pace loader.')
            ->children();
        $pace->scalarNode('ajax')->defaultValue(true);
        $pace->scalarNode('document')->defaultValue(true);
        $pace->scalarNode('eventLag')->defaultValue(true);
        $pace->scalarNode('restartOnPushState')->defaultValue(true);
        $pace->scalarNode('restartOnRequestAfter')->defaultValue(true);

        $rootNode->booleanNode('ajax_forms')->defaultValue(false)->info('Use ajax forms in bundles that support this.');
        $rootNode->variableNode('icons')->defaultValue(['fontawesome'])->info('Icon libraries to load, available: fontawesome, glyphicons. @note: glyphicons are always loaded with bootstrap');

        $this->createAssetConfig(
            $rootNode,
            [
                'jquery' => 'bundles/ynloframework/vendor/jquery/jquery.min.js',
                'jquery_form' => 'bundles/ynloframework/vendor/jquery.form/jquery.form.js',
                'pace_css' => 'bundles/ynloframework/vendor/pace/pace.css',
                'bootstrap_js' => 'bundles/ynloframework/vendor/bootstrap/js/bootstrap.min.js',
                'bootstrap_css' => 'bundles/ynloframework/vendor/bootstrap/css/bootstrap.min.css',
                'fontawesome' => 'bundles/ynloframework/vendor/font-awesome/css/font-awesome.min.css',
                'glyphicons' => 'bundles/ynloframework/vendor/glyphicons/css/glyphicons.css',
                'icomoon' => 'bundles/ynloframework/vendor/icomoon/icomoon.css',
            ]
        );

        /** @var NodeBuilder $mappings */
        $mappings = $rootNode
            ->arrayNode('assets_contexts')
            ->useAttributeAsKey('id')
            ->example(['app' => ['include' => ['all'], 'exclude' => ['bundle_ynlo_admin']]])
            ->prototype('array')
            ->children();

        $mappings->arrayNode('include')->info('Array of assets to include, only this assets will be used.')->end();
        $mappings->arrayNode('exclude')->info('Array of assets to exclude.')->end();
        $mappings->arrayNode('override')->useAttributeAsKey('id')->prototype('scalar')->info('Array of named assets to override')->end();
        $mappings->end();

        return $treeBuilder;
    }
}
