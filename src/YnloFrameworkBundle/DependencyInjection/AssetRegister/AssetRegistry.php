<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\Configuration;

/**
 * Class AssetRegistry.
 */
class AssetRegistry
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var array|AsseticAsset[]
     */
    private $namedAssets = [];

    /**
     * @var array
     */
    private $namedAssetsByExtension = [];

    /**
     * AssetRegistry constructor.
     *
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;

        //first pass to allow other extensions to modify the framework assets settings
        $this->prependAssetsConfigs($container);

        $this->resolveRegisteredAssets();
    }

    /**
     * Verify if given named asset exist.
     *
     * @param string $name
     *
     * @return array
     */
    public function hasNamedAsset($name)
    {
        return array_key_exists($name, $this->namedAssets);
    }

    /**
     * @param string $name
     *
     * @return AsseticAsset
     */
    public function getNamedAsset($name)
    {
        if ($this->hasNamedAsset($name)) {
            return $this->namedAssets[$name];
        }
        $msg = sprintf('There are not registered asset called `%s`', $name);
        throw new \RuntimeException($msg);
    }

    /**
     * Get the list of assets in assetic array format.
     *
     * @return array
     */
    public function getAsseticAssetsArray()
    {
        $finalAssets = [];
        foreach ($this->namedAssets as $name => $asset) {
            foreach ($asset->getInputs() as $input) {
                if (!isset($finalAssets[$name]['inputs']) || !in_array($input, $finalAssets[$name]['inputs'], true)) {
                    $finalAssets[$name]['inputs'][] = $input;
                }
            }
            foreach ($asset->getFilters() as $filter) {
                if (!isset($finalAssets[$name]['filters']) || !in_array($filter, $finalAssets[$name]['filters'], true)) {
                    $finalAssets[$name]['filters'][] = $filter;
                }
            }
        }

        return $finalAssets;
    }

    /**
     * Create and register a asset bundle for array of assets given
     * the bundle prefix is used to create different versions of the same bundle,
     * e.g. admin -> admin_js and admin_css
     * Assets are separated in different named assets.
     *
     * @param string               $bundlePrefix
     * @param array|AsseticAsset[] $assets
     */
    public function registerAssetBundle($bundlePrefix, array $assets)
    {
        $jsAssets = [];
        $jsFilters = [];
        $cssAssets = [];
        $cssFilters = [];
        /** @var AsseticAsset $asset */
        foreach ($assets as $asset) {
            if ($asset->isJavascript()) {
                $jsAssets = array_unique(array_merge($jsAssets, $asset->getInputs()));
                $jsFilters = array_unique(array_merge($jsFilters, $asset->getFilters()));
            } else {
                $cssAssets = array_unique(array_merge($cssAssets, $asset->getInputs()));
                $cssFilters = array_unique(array_merge($cssFilters, $asset->getFilters()));
            }
        }
        $js = new AsseticAsset(sprintf('%s_js', $bundlePrefix), $jsAssets, $jsFilters);
        $css = new AsseticAsset(sprintf('%s_css', $bundlePrefix), $cssAssets, $cssFilters);
        $this->namedAssets[$js->getName()] = $js;
        $this->namedAssets[$css->getName()] = $css;
    }

    /**
     * Resolve all registered assets.
     */
    private function resolveRegisteredAssets()
    {
        $this->namedAssetsByExtension = [];
        foreach ($this->container->getExtensions() as $extension) {
            $extensionAssets = [];
            if ($extension instanceof AssetRegisterInterface) {
                preg_match('/([\w\\\]+\\\)\w+/', get_class($extension), $matches);
                if (isset($matches[1]) && $namespace = $matches[1]) {
                    $configClass = "{$namespace}Configuration";
                    $configuration = new $configClass();

                    $processor = new Processor();
                    $config = $processor->processConfiguration($configuration, $this->container->getExtensionConfig($extension->getAlias()));

                    //find for "assets" node in the root of bundle config
                    if (isset($config['assets']) && $config['assets']['enabled']) {
                        foreach ($config['assets'] as $name => $asset) {
                            if ($name !== 'enabled') {
                                $extensionAssets[] = new AsseticAsset($name, [$asset]);
                            }
                        }
                    }

                    $assetsNotIndexed = array_merge($extensionAssets, $extension->registerInternalAssets() ?: []);
                    //index assets to apply filter later easier
                    $indexedAssets = [];
                    foreach ($assetsNotIndexed as $asset) {
                        if ($asset instanceof AsseticAsset) {
                            $indexedAssets[$asset->getName()] = $asset;
                        }
                    }
                    $filteredAssets = $extension->filterAssets($indexedAssets, $config) ?: $config['assets'];
                    $this->namedAssets = array_merge($this->namedAssets, $filteredAssets);
                    $this->namedAssetsByExtension[$extension->getAlias()] = $filteredAssets;
                }
            }
        }

        $this->registerAssetBundle('all', $this->namedAssets);

        foreach ($this->namedAssetsByExtension as $extension => $assets) {
            $this->registerAssetBundle(sprintf('bundle_'.$extension), $assets);
        }
    }

    /**
     * Execute a prepend method in any extensions implementing
     * AssetRegisterInterface and PrependExtensionInterface
     * this method is used to allow override the framework assets configuration in other extensions
     * e.g. YnloPjaxExtension enable ajax_forms automatically in the framework when pjax is enabled.
     *
     * @param ContainerBuilder $container
     */
    private function prependAssetsConfigs(ContainerBuilder $container)
    {
        foreach ($container->getExtensions() as $extension) {
            if ($extension instanceof AssetRegisterInterface) {
                if ($extension instanceof PrependExtensionInterface && $extension->getAlias() !== 'ynlo_framework') {
                    $extension->prepend($container);
                }
            }
        }
    }
}
