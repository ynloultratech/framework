<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister;

interface AssetRegisterInterface
{
    /**
     * Use this to register assets to compile by assetic.
     * This assets are added silently to assetic and can`t be configured or changed.
     * Use only for internal required libraries.
     *
     * @return array|AsseticAsset[]
     */
    public function registerInternalAssets();

    /**
     * Receive array of assets registered for current extension
     * and should return the same array filtered with only assets to compile based on the extension config.
     *
     * e.g:
     *
     * config.yml
     *  pace: false
     *  assets:
     *    pace_js: '..pace.js'
     *
     * base on this config the "pace_js" asset should be deleted because pace has been disabled completely
     *
     * @param array $assets
     * @param array $config
     *
     * @return mixed
     */
    public function filterAssets(array $assets, array $config);
}
