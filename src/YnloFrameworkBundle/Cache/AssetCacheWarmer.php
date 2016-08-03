<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Cache;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

/**
 * Touch a asset to force rebuild,
 * helpful when assets is modified via config
 * in dev environment assets are not updated
 */
class AssetCacheWarmer extends CacheWarmer
{
    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $js = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Resources', 'public', 'js', 'framework.js']);
        touch($js);
        $css = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Resources', 'public', 'css', 'ynlo-admin.css']);
        touch($css);
    }
}
