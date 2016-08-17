<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * Class AssetFactory.
 */
class AssetFactory
{
    /**
     * create.
     *
     * @param string $name
     * @param string $path
     * @param array  $filters
     *
     * @return Javascript|Stylesheet
     */
    public static function asset($name, $path, array $filters = [])
    {
        if (preg_match('/js$/', $path)) {
            return new Javascript($name, $path, $filters);
        } else {
            return new Stylesheet($name, $path, $filters);
        }
    }

    /**
     * @param string $name
     * @param string $assetPath
     *
     * @return JavascriptModule
     */
    public static function module($name, $assetPath)
    {
        return new JavascriptModule($name, $assetPath);
    }
}
