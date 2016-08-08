<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Class AssetRegistry.
 */
class AssetRegistry
{
    /**
     * @var ContainerBuilder
     */
    private static $container;

    /**
     * @var array|AssetInterface[]|AssetBundle[]
     */
    private static $assets = [];

    /**
     * AssetRegistry builder.
     *
     * @param ContainerBuilder $container
     */
    public static function build(ContainerBuilder $container)
    {
        self::$container = $container;

        //first pass to allow other extensions to modify the framework assets settings
        self::prependAssetsConfigs($container);

        self::resolveRegisteredAssets();
    }

    /**
     * Return serialized registry
     * used save as parameter in the container
     *
     * @return string
     */
    public static function serialize()
    {
        return serialize(
            [
                'assets' => self::$assets,
            ]
        );
    }

    /**
     * Re-build the registry using a serialized string
     *
     * @param array $serializedRegistry
     */
    public static function unserialize($serializedRegistry)
    {
        self::$assets = unserialize($serializedRegistry)['assets'];
    }

    /**
     * Verify if given named assets exist.
     *
     * @param string $name
     *
     * @return array
     */
    public static function hasNamedAsset($name)
    {
        return array_key_exists($name, self::$assets);
    }

    /**
     * @return array|AssetInterface[]|AssetBundle[]
     */
    public static function getAssets()
    {
        return self::$assets;
    }

    /**
     * addAsset
     *
     * @param AssetInterface $asset
     */
    public static function addAsset(AssetInterface $asset)
    {
        self::$assets[$asset->getName()] = $asset;
        self::refreshBundleAll();
    }

    /**
     * @param string $name
     */
    public static function removeAsset($name)
    {
        unset(self::$assets[$name]);
        self::refreshBundleAll();
    }

    /**
     * @param AbstractAsset|AbstractAsset[]|array $assets
     */
    public static function prependAssets($assets)
    {
        if (is_array($assets)) {
            foreach (array_reverse($assets) as $asset) {
                self::prependAssets($asset);
            }
        } else {
            if (self::hasNamedAsset($assets->getName())) {
                self::removeAsset($assets->getName());
            }
            self::$assets = array_merge([$assets->getName() => $assets], self::$assets);
            self::refreshBundleAll();
        }
    }

    /**
     * Get the list of assets in assetic array format.
     *
     * @return array
     */
    public static function getAsseticAssetsArray()
    {
        $finalAssets = [];
        foreach (self::$assets as $name => $asset) {
            if ($asset instanceof AssetBundle) {
                $finalAssets[$asset->getName()] = [
                    'inputs' => $asset->getPaths(),
                    'filters' => $asset->getFilters(),
                ];
            } else {
                $finalAssets[$name] = [
                    'inputs' => $asset->getPath(),
                    'filters' => ($asset instanceof AbstractAsseticAsset) ? $asset->getFilters() : [],
                ];
            }
        }

        return $finalAssets;
    }

    /**
     * Create and register a assets bundle for array of assets given
     * the bundle prefix is used to create different versions of the same bundle,
     * e.g. admin -> admin_js and admin_css
     * Assets are separated in different named assets.
     *
     * @param string                $bundlePrefix  prefix tu use in the bundle, assets has been separated in prefix_css and prefix_js
     * @param array|AbstractAsset[] $assets        array of assets to create the bundle
     * @param boolean               $ignoreModules assets marked as javascript module is not compiled into the bundle
     */
    public static function registerAssetBundle($bundlePrefix, array $assets, $ignoreModules = false)
    {
        $jsAssets = [];
        $cssAssets = [];
        /** @var AbstractAsset $asset */
        foreach ($assets as $asset) {
            if ($ignoreModules && $asset instanceof JavascriptModule) {
                continue;
            }

            if ($asset instanceof Javascript) {
                $jsAssets[] = $asset;
            } elseif ($asset instanceof Stylesheet) {
                $cssAssets[] = $asset;
            } elseif ($asset instanceof AssetBundle) {
                foreach ($asset->getAssets() as $assetInBundle) {
                    if ($ignoreModules && $assetInBundle instanceof JavascriptModule) {
                        continue;
                    }

                    if ($assetInBundle instanceof Javascript) {
                        $jsAssets[] = $assetInBundle;
                    } elseif ($assetInBundle instanceof Stylesheet) {
                        $cssAssets[] = $assetInBundle;
                    }
                }
            }
        }
        $js = new AssetBundle(sprintf('%s_js', $bundlePrefix), $jsAssets);
        $css = new AssetBundle(sprintf('%s_css', $bundlePrefix), $cssAssets);
        self::$assets[$js->getName()] = $js;
        self::$assets[$css->getName()] = $css;
    }

    /**
     * @param string $name
     *
     * @return AbstractAsset
     */
    public static function getAsset($name)
    {
        if (self::hasNamedAsset($name)) {
            return self::$assets[$name];
        }
        $msg = sprintf('There are not registered assets called `%s`', $name);
        throw new \RuntimeException($msg);
    }

    /**
     * Resolve all registered assets.
     */
    private static function resolveRegisteredAssets()
    {
        $namedAssetsByExtension = [];
        foreach (self::$container->getExtensions() as $extension) {
            if ($extension instanceof AssetRegisterInterface) {
                preg_match('/([\w\\\]+\\\)\w+/', get_class($extension), $matches);
                if (isset($matches[1]) && $namespace = $matches[1]) {
                    $configClass = "{$namespace}Configuration";
                    $configuration = new $configClass();

                    $processor = new Processor();
                    $config = $processor->processConfiguration($configuration, self::$container->getExtensionConfig($extension->getAlias()));

                    $assetsNotIndexed = $extension->registerAssets($config, self::$container);

                    $indexedAssets = [];
                    foreach ($assetsNotIndexed as $asset) {
                        if ($asset instanceof AbstractAsset) {
                            $indexedAssets[$asset->getName()] = $asset;
                        }
                    }
                    self::$assets = array_merge(self::$assets, $indexedAssets);
                    $namedAssetsByExtension[$extension->getAlias()] = $indexedAssets;
                }
            }
        }

        self::registerConfigAssets();

        self::refreshBundleAll();

        foreach ($namedAssetsByExtension as $extension => $assets) {
            self::registerAssetBundle(sprintf('bundle_'.$extension), $assets, true);
        }
    }

    /**
     * Register all assets and modules given in the asset configuration
     */
    private static function registerConfigAssets()
    {
        $config = self::$container->getExtensionConfig('ynlo_assets')[0];

        if (isset($config['assets'])) {
            foreach ($config['assets'] as $name => $asset) {
                self::addAsset(AssetFactory::asset($name, $asset));
            }
        }

        if (isset($config['modules'])) {
            foreach ($config['modules'] as $name => $assetConfig) {
                if (!array_key_value($assetConfig, 'asset')) {
                    $msg = sprintf('The registered javascript module `%s` don`t have a valid asset associated.', $name);
                    throw new \RuntimeException($msg);
                }
                $module = AssetFactory::module($name, $assetConfig['asset']);
                $module->setDependencies(array_key_value($assetConfig, 'deps', []));
                $module->setCdn(array_key_value($assetConfig, 'cdn'));
                $module->setExports(array_key_value($assetConfig, 'exports', []));
                $module->setInit(array_key_value($assetConfig, 'init'));
                $module->setJqueryPlugins(array_key_value($assetConfig, 'jquery_plugins', []));
                self::addAsset($module);
            }
        }
    }

    /**
     * refreshBundleAll
     */
    private static function refreshBundleAll()
    {
        self::registerAssetBundle('all', self::$assets, true);
    }

    /**
     * Execute a prepend method in any extensions implementing
     * AssetRegisterInterface and PrependExtensionInterface
     * this method is used to allow override the assets configuration in other extensions
     * e.g. YnloPjaxExtension enable ajax_forms automatically in the framework when pjax is enabled.
     *
     * @param ContainerBuilder $container
     */
    private static function prependAssetsConfigs(ContainerBuilder $container)
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
