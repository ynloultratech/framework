<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ynlo_framework')->children();

        /* @noinspection UnNecessaryDoubleQuotesInspection */
        $rootNode->scalarNode('debug')->defaultValue('false');
        $rootNode->booleanNode('animate_css')->defaultValue(true)->info('Include Animate.css');
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

        return $treeBuilder;
    }
}
