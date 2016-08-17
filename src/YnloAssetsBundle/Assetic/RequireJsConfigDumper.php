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
 * This filter is used to dump the locations and settings fo all js Modules.
 */
class RequireJsConfigDumper implements FilterInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * RequireJsConfigDumper constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
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
        if (preg_match('/require_js_config\.js$/', $asset->getSourcePath())) {
            $config = [
                'baseUrl' => '/',
                'waitSeconds' => false,
                'paths' => [],
                'shim' => [],
            ];

            foreach (AssetRegistry::getAssets() as $module) {
                if ($module instanceof JavascriptModule) {
                    $path = (array_key_value($this->config, 'cdn') && $module->getCdn()) ? $module->getCdn() : $module->getPath();
                    $config['paths'][$module->getModuleName()] = preg_replace('/\.js$/', '', $path);
                    if ($module->getDependencies()) {
                        $config['shim'][$module->getModuleName()]['deps'] = $module->getDependencies();
                    }
                    if ($module->getExports()) {
                        $config['shim'][$module->getModuleName()]['exports'] = $module->getExports();
                    }
                    if ($module->getInit()) {
                        $config['shim'][$module->getModuleName()]['init'] = '{function}'.$module->getInit().'{/function}';
                    }
                }
            }

            $configJson = json_encode($config, JSON_UNESCAPED_SLASHES);
            $configJs = str_replace(
                [
                    '"{function}',
                    '{/function}"',
                    '\"',
                ],
                [
                    'function(){',
                    '}',
                    '"',
                ],
                $configJson
            );

            $content
                = <<<EOS
require.config($configJs);
EOS;

            $asset->setContent($content);
        }
    }
}
