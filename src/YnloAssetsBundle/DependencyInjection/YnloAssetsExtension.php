<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloAssetsBundle\Assets\AssetContext;
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegistry;

/**
 * YnloAssetsExtension.
 */
class YnloAssetsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.assets.config', $config);

        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('assetic')) {
            throw new \LogicException('The assetic bundle is required.');
        }

        $asseticConfig = $container->getExtensionConfig('assetic')[0];

        //Assetic base configuration
        $asseticConfig['bundles'][] = 'YnloFrameworkBundle';
        $asseticConfig['filters']['cssrewrite'] = null;
        if (empty($asseticConfig['assets'])) {
            $asseticConfig['assets'] = [];
        }

        AssetRegistry::build($container);
        AssetRegistry::prependAssets(
            [
                AssetFactory::asset('requirejs', 'bundles/ynloassets/vendor/requirejs/require.min.js'),
                AssetFactory::asset('requirejs_config', 'bundles/ynloassets/js/require_js_config.js', ['require_js_config']),
            ]
        );

        AssetRegistry::addAsset(
            AssetFactory::asset('jquery_plugins_overrides', 'bundles/ynloassets/js/jquery_plugins_overrides.js', ['jquery_plugin_override'])
        );

        $this->processAssetContexts($container);

        //save the array of assets in a param to restore the registry later
        $container->setParameter('ynlo.assets', AssetRegistry::serialize());
        $registeredAssets = AssetRegistry::getAsseticAssetsArray();
        $asseticConfig['assets'] = array_merge($registeredAssets, $asseticConfig['assets']);

        $container->prependExtensionConfig('assetic', $asseticConfig);
    }

    private function processAssetContexts(ContainerBuilder $containerBuilder)
    {
        $config = $this->processConfiguration(new Configuration(), $containerBuilder->getExtensionConfig('ynlo_assets'));
        $defaultContexts = [
            'app' => [
                'include' => ['all'],
                'exclude' => ['bundle_ynlo_admin'],
            ],
        ];

        $config['contexts'] = array_merge_recursive($config['contexts'], $defaultContexts);

        foreach ($config['contexts'] as $context => $contextConfig) {
            $context = new AssetContext($context);
            $context->setInclude(array_key_value($contextConfig, 'include', []));
            $context->setExclude(array_key_value($contextConfig, 'exclude', []));
            $context->setOverride(array_key_value($contextConfig, 'override', []));

            if (empty($contextConfig['include'])) {
                $msg = sprintf('The context `%s` don`t have any assets to include.', $context);
                throw new \LogicException($msg);
            }

            AssetRegistry::registerAssetBundle($context->getName(), $context->getAssets());
        }
    }
}
