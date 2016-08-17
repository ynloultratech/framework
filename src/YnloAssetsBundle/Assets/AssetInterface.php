<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * AssetInterface.
 */
interface AssetInterface
{
    const JAVASCRIPT = 'js';
    const STYLESHEET = 'css';

    /**
     * Name to represent the asset.
     *
     * @return string
     */
    public function getName();

    /**
     * Array of assets.
     *
     * @return array
     */
    public function getPath();

    /**
     * One of the AssetInterface:: constants.
     *
     * @return mixed
     */
    public function getType();
}
