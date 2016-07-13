<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

class YnloFrameworkExtension extends Extension implements PrependExtensionInterface, AssetRegisterInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.config', $config);
        $container->setParameter('ynlo.js_plugin.core', [
            'debug' => $config['debug']
        ]);

        $configDir = __DIR__ . '/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependAsseticAssets($container);
    }

    protected function prependAsseticAssets(ContainerBuilder $container)
    {
        if (!$container->hasExtension('assetic')) {
            throw new \LogicException('The assetic bundle is required by YnloFramework bundle to work');
        }

        $asseticConfig = $container->getExtensionConfig('assetic')[0];

        //Assetic base configuration
        $asseticConfig['bundles'][] = 'YnloFrameworkBundle';
        $asseticConfig['filters']['cssrewrite'] = null;
        if (empty($asseticConfig['assets'])) {
            $asseticConfig['assets'] = [];
        }

        //first pass to allow other extensions to modify the framework assets settings
        foreach ($container->getExtensions() as $extension) {
            if ($extension instanceof AssetRegisterInterface) {
                if ($extension instanceof PrependExtensionInterface && $extension->getAlias() !== 'ynlo_framework') {
                    $extension->prepend($container);
                }
            }
        }

        $assetsByExtension = [];
        foreach ($container->getExtensions() as $extension) {
            $extensionAssets = [];
            if ($extension instanceof AssetRegisterInterface) {

                //find for "assets" node in the root of bundle config
                preg_match('/([\w\\\]+\\\)\w+/', get_class($extension), $matches);
                if (isset($matches[1]) && $namespace = $matches[1]) {
                    $configClass = "{$namespace}Configuration";
                    $configuration = new $configClass;

                    $config = $this->processConfiguration($configuration, $container->getExtensionConfig($extension->getAlias()));
                    if (isset($config['assets']) && $config['assets']['enabled']) {
                        $filteredAssets = $extension->filterConfigurationAssets($config['assets'], $config);
                        foreach ($filteredAssets as $name => $asset) {
                            if ($name !== 'enabled') {
                                $extensionAssets[] = new AsseticAsset($name, [$asset]);
                            }
                        }
                    }
                }

                $assetsByExtension[$extension->getAlias()] = array_merge($extensionAssets, $extension->registerInternalAssets());
            }
        }

        $finalAssets = [];
        $finalAssets['ynlo_framework_all_js'] = [];
        $finalAssets['ynlo_framework_all_css'] = [];
        foreach ($assetsByExtension as $extensionName => $extensionAssets) {
            //create extension assets groups
            $finalAssets['bundle_' . $extensionName . '_' . 'js'] = [];
            $finalAssets['bundle_' . $extensionName . '_' . 'css'] = [];
            $finalAssets['bundle_' . $extensionName . '_' . 'css'] = [];

            /** @var AsseticAsset $asset */
            foreach ($extensionAssets as $asset) {
                $groups = [
                    $asset->getName(),
                    'bundle_' . $extensionName . '_' . $asset->getType(),
                    'ynlo_framework_all_' . $asset->getType()
                ];

                foreach ($groups as $group) {
                    foreach ($asset->getInputs() as $input) {
                        if (!isset($finalAssets[$group]['inputs']) || !in_array($input, $finalAssets[$group]['inputs'], true)) {
                            $finalAssets[$group]['inputs'][] = $input;
                        }
                    }
                    foreach ($asset->getFilters() as $filter) {
                        if (!isset($finalAssets[$group]['filters']) || !in_array($filter, $finalAssets[$group]['filters'], true)) {
                            $finalAssets[$group]['filters'][] = $filter;
                        }
                    }
                }
            }
        }
        $asseticConfig['assets'] = array_merge($finalAssets, $asseticConfig['assets']);
        $container->setParameter('ynlo.assetic.assets', $asseticConfig['assets']);
        $container->prependExtensionConfig('assetic', $asseticConfig);
    }

    /**
     * @inheritDoc
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_framework_js', [
                'bundles/ynloframework/js/framework.js',
                'bundles/ynloframework/js/core.yfp.js',
                'bundles/ynloframework/js/lib/*'
            ], ['yfp_config_dumper'])
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterConfigurationAssets(array $assets, array $config)
    {
        if ($config['pace'] === false) {
            unset($assets['pace_js'], $assets['pace_css']);
        }
        if ($config['ajax_forms'] === false) {
            unset($assets['jquery_form']);
        }
        return $assets;
    }
}