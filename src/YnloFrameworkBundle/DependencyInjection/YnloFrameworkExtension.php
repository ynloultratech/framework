<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetContext;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegistry;

class YnloFrameworkExtension extends Extension implements PrependExtensionInterface, AssetRegisterInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.config', $config);
        $container->setParameter(
            'ynlo.js_plugin.core',
            [
                'debug' => $config['debug'],
            ]
        );

        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependAsseticAssets($container);
    }

    /**
     * {@inheritdoc}
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset(
                'ynlo_framework_js',
                [
                    'bundles/ynloframework/js/framework.js',
                    'bundles/ynloframework/js/core.yfp.js',
                    'bundles/ynloframework/js/lib/*',
                ],
                [
                    'yfp_config_dumper',
                ]
            ),
            new AsseticAsset('pace_js', 'bundles/ynloframework/vendor/pace/pace.js', ['pace_settings_dumper']),
            new AsseticAsset('ynlo_debugger_js', 'bundles/ynloframework/js/debugger.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_debugger_css', 'bundles/ynloframework/css/debugger.css'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filterAssets(array $assets, array $config)
    {
        if ($config['pace'] === false) {
            unset($assets['pace_js'], $assets['pace_css']);
        }
        if ($config['ajax_forms'] === false) {
            unset($assets['jquery_form']);
        }

        $iconSets = [
            'fontawesome',
            'glyphicons',
            'icomoon',
        ];
        foreach ($iconSets as $iconSet) {
            if ((is_string($config['icons']) && $config['icons'] !== $iconSet)
                || (is_array($config['icons']) && !in_array($iconSet, $config['icons'], true))
            ) {
                unset($assets[$iconSet]);
            }
        }

        if ($config['debug'] == false) {
            unset($assets['ynlo_debugger']);
        }

        return $assets;
    }

    protected function prependAsseticAssets(ContainerBuilder $container)
    {
        if (!$container->hasExtension('assetic')) {
            throw new \LogicException('The assetic bundle is required by YnloFramework bundle to work');
        }

        $asseticConfig = $container->getExtensionConfig('assetic')[0];

        //Assetic base configuration
        $asseticConfig['bundles'][] = 'YnloFrameworkBundle';
        if ($container->hasExtension('ynlo_admin')) {
            $asseticConfig['bundles'][] = 'YnloAdminBundle';
        }
        $asseticConfig['filters']['cssrewrite'] = null;
        if (empty($asseticConfig['assets'])) {
            $asseticConfig['assets'] = [];
        }

        $registry = new AssetRegistry($container);
        $this->processAssetContexts($registry, $container);
        $registeredAssets = $registry->getAsseticAssetsArray();

        $asseticConfig['assets'] = array_merge($registeredAssets, $asseticConfig['assets']);
        $container->setParameter('ynlo.assetic.assets', $asseticConfig['assets']);
        $container->prependExtensionConfig('assetic', $asseticConfig);
    }

    private function processAssetContexts(AssetRegistry $registry, ContainerBuilder $containerBuilder)
    {
        $config = $this->processConfiguration(new Configuration(), $containerBuilder->getExtensionConfig('ynlo_framework'));
        $defaultContexts = [
            'app' => [
                'include' => ['all'],
                'exclude' => ['bundle_ynlo_admin'],
            ],
            'admin' => [
                'include' => ['all'],
            ],
        ];

        $config['assets_contexts'] = array_merge_recursive($config['assets_contexts'], $defaultContexts);

        foreach ($config['assets_contexts'] as $context => $contextConfig) {
            $context = new AssetContext($context, $registry);
            $context->setInclude(array_key_value($contextConfig, 'include', []));
            $context->setExclude(array_key_value($contextConfig, 'exclude', []));
            $context->setOverride(array_key_value($contextConfig, 'override', []));

            if (empty($contextConfig['include'])) {
                $msg = sprintf('The context `%s` don`t have any assets to include.', $context);
                throw new \LogicException($msg);
            }

            $registry->registerAssetBundle($context->getName(), $context->getAssets());
        }
    }
}
