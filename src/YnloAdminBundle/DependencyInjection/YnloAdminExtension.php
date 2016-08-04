<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

class YnloAdminExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'ynlo.js_plugin.admin',
            [
                'icheck' => $config['icheck'],
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
        //auto-configure block bundle
        $vendorConfig = $container->getExtensionConfig('sonata_block')[0];
        $vendorConfig['default_contexts'][] = 'admin';
        $vendorConfig['blocks']['sonata.admin.block.admin_list']['contexts'][] = 'admin';
        $container->prependExtensionConfig('sonata_block', $vendorConfig);

        //set twig globals
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig('ynlo_admin'));
        $vendorConfig = $container->getExtensionConfig('twig')[0];
        $vendorConfig['globals']['admin_body_classes'][] = 'skin-'.$config['skin'];

        if ($config['mode'] === 'fixed') {
            $vendorConfig['globals']['admin_body_classes'][] = 'fixed';
        } else {
            $vendorConfig['globals']['admin_body_classes'][] = 'layout-boxed';
        }

        if ($config['sidebar_mini']) {
            $vendorConfig['globals']['admin_body_classes'][] = 'sidebar-mini';
        }

        if ($config['collapsed_sidebar']) {
            $vendorConfig['globals']['admin_body_classes'][] = 'sidebar-collapse';
        }

        $container->prependExtensionConfig('twig', $vendorConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_admin_js', 'bundles/ynloadmin/js/admin.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_admin_css', 'bundles/ynloadmin/css/ynlo-admin.css', ['yfp_config_dumper']),
            new AsseticAsset('sonata_admin_override_js', 'bundles/ynloadmin/js/sonata_admin_override.js'),
            new AsseticAsset('ynlo_admin_list_batch_js', 'bundles/ynloadmin/js/admin_list_batch.yfp.js', ['yfp_config_dumper']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filterAssets(array $assets, array $config)
    {
        //resolve icheck theme
        if ($config['icheck'] && is_string($config['icheck']) && $config['icheck'] !== 'flat-blue') {
            $theme = $config['icheck'];

            if (strpos($theme, '-') !== false) {
                $theme = str_replace('-', '/', $theme);
            } else {
                $theme = $theme.'/'.$theme;
            }
            $originalTheme = $assets['icheck_theme_css']->getInputs()[0];
            $newTheme = str_replace('flat/blue', $theme, $originalTheme);
            $assets['icheck_theme_css'] = new AsseticAsset('icheck_theme_css', $newTheme);
        } elseif (!$config['icheck']) {
            unset($assets['icheck_js'], $assets['icheck_theme_css']);
        }

        return $assets;
    }
}
