<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * This assetic filter is used for YnloFramework javascript plugins
 * to dump all settings from the config.yml to the javascript file.
 *
 * This filter using naming convention to resolve settings and dump
 *
 * - Must create a parameter with all settings to dump when read config.yml
 * - The parameter with array of settings should be called "ynlo.js_plugin.plugin_name"
 * - The javascript file in assetic should be named "plugin_name.yfp.js" : (*.yfp.js)
 * - The name of the plugin inside javascript will be resolved using CamelCase version plugin_name -> PluginName
 */
class FrameworkPluginSettingsDumper implements FilterInterface
{
    /**
     * @var ParameterBag
     */
    protected $parameterBag;

    public function __construct(ParameterBag $parameters)
    {
        $this->parameterBag = $parameters;
    }

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
        if (preg_match('/\.yfp\.js$/', $asset->getSourcePath())) {
            $content = $asset->getContent();

            preg_match('/(\w+)\.yfp\.js$/', $asset->getSourcePath(), $matches);
            if (isset($matches[1])) {
                $name = $matches[1];
                $pluginName = str_replace(' ', '', ucwords(strtr($matches[1], '_-', '  ')));
                $config = [];
                if ($this->parameterBag->has("ynlo.js_plugin.$name")) {
                    $config = $this->parameterBag->get("ynlo.js_plugin.$name");
                }
                $jsonConfig = json_encode($config, JSON_FORCE_OBJECT);

                $autoRegister = null;
                if (strpos($content, "YnloFramework.register('$pluginName')") === false
                    && strpos($content, "YnloFramework.register(\"$pluginName\')") === false
                ) {
                    $autoRegister = "\nYnloFramework.register('$pluginName');";
                }

                $settings
                    = <<<JAVASCRIPT
$autoRegister
YnloFramework.$pluginName.config = $.extend({}, YnloFramework.$pluginName.config, $jsonConfig);

JAVASCRIPT;

                $asset->setContent($content.$settings);
            }
        }
    }
}
