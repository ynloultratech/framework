<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Create a asset configuration inside your extension config
 */
trait AssetConfiguration
{
    /**
     * @param NodeBuilder $root
     * @param array       $assets array of assets with name as array key and asset as value
     *
     * @return ArrayNodeDefinition
     */
    protected function createAssetConfig(NodeBuilder $root, $assets = [])
    {
        $assetsNode = $root->arrayNode('assets')->canBeDisabled()->addDefaultsIfNotSet();
        foreach ($assets as $name => $asset) {
            $assetsNode->children()->scalarNode($name)->defaultValue($asset);
        }

        return $assetsNode;
    }
}