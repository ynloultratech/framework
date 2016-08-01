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
        $rootNode = $treeBuilder->root('ynlo_admin')->addDefaultsIfNotSet()->children();

        return $treeBuilder;
    }
}
