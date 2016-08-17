<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegistry;
use YnloFramework\YnloAssetsBundle\Assets\JavascriptModule;

/**
 * This filter override some jquery plugins to load when is used with requireJs.
 */
class JqueryPluginOverride implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        if (preg_match('/jquery_plugins_overrides\.js$/', $asset->getSourcePath())) {
            $content = '';
            foreach (AssetRegistry::getAssets() as $module) {
                if (($module instanceof JavascriptModule) && $module->getJqueryPlugins()) {
                    $name = $module->getModuleName();
                    foreach ($module->getJqueryPlugins() as $method) {
                        $script
                            = <<<JAVASCRIPT
//Override $name plugin to load with RequireJs
$.fn.$method = function () {
    var element = $(this);
    var args = arguments;
    require(['$name'], function (module) {
        return element.each(function () {
            $.fn.$method.apply(element,args);
        });
    });
    
    return element;
};

JAVASCRIPT;
                        $content .= $script;
                    }
                    $asset->setContent($content);
                }
            }
        }
    }
}
