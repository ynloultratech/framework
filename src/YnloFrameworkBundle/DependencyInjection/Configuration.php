<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection;

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

        $rootNode->booleanNode('debug')->defaultValue("%kernel.debug%");
        $rootNode->booleanNode('pace')->defaultValue(true)->info('Enable or disable the Pace loader.');
        $rootNode->booleanNode('ajax_forms')->defaultValue(false)->info('Use ajax forms in bundles that support this.');
        $rootNode->variableNode('icons')->defaultValue(['fontawesome'])->info('One or more than one icon library, available: fontawesome, glyphicons', 'icomoon');

        $this->createAssetConfig(
            $rootNode, [
                'jquery' => 'bundles/ynloframework/js/vendor/jquery/jquery.min.js',
                'jquery_form' => 'bundles/ynloframework/js/vendor/jquery.form/jquery.form.js',
                'pace_js' => 'bundles/ynloframework/js/vendor/pace/pace.js',
                'pace_css' => 'bundles/ynloframework/js/vendor/pace/pace.css',
                'bootstrap_js' => 'bundles/ynloframework/js/vendor/bootstrap/js/bootstrap.min.js',
                'bootstrap_css' => 'bundles/ynloframework/js/vendor/bootstrap/css/bootstrap.min.css',
                'fontawesome' => 'bundles/ynloframework/js/vendor/font-awesome/css/font-awesome.min.css',
                'glyphicons' => 'bundles/ynloframework/js/vendor/glyphicons/css/glyphicons.css',
                'icomoon' => 'bundles/ynloframework/js/vendor/icomoon/icomoon.css',
            ]
        );

        return $treeBuilder;
    }
}