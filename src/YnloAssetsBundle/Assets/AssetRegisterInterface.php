<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Interface AssetRegisterInterface.
 */
interface AssetRegisterInterface
{
    /**
     * Register specific bundle assets.
     *
     * @param array            $config           array of current bundle configuration, use this to filter assets based on current config.
     * @param ContainerBuilder $containerBuilder instance of ContainerBuilder if you depend another bundle config or need more complex logic
     *
     * @return array|AssetInterface[]
     */
    public function registerAssets(array $config, ContainerBuilder $containerBuilder);
}
