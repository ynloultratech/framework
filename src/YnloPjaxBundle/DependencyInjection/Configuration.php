<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloPjaxBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ynlo_pjax')->addDefaultsIfNotSet()->children();

        $rootNode->scalarNode('target')->defaultValue('body')->end();

        $rootNode->scalarNode('forms')->defaultValue('form:not([data-pjax="false"])');
        $rootNode->scalarNode('links')->defaultValue('a:not([data-pjax="false"])a:not([href="#"])a:not([target="_blank"])');
        $rootNode->scalarNode('remove_ajax_header')->defaultValue(true)->info('Ajax headers "X-Requested-With" will be removed in all requests.')->end();
        $rootNode->scalarNode('autospin')->defaultValue(true)->info('Append spinicon for buttons and other elements while page is loading.')->end();
        $rootNode->scalarNode('spinicon')->defaultValue('fa fa-spinner fa-pulse')->end();

        return $treeBuilder;
    }
}