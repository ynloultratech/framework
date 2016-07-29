<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

class YnloModalExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.modal.config', $config);
        $container->setParameter(
            'ynlo.js_plugin.modal', [
            'spinicon' => $config['spinicon'],
            'loaderTemplate' => $config['loaderTemplate'],
            'loaderDialogClass' => $config['loaderDialogClass'],
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
        $config = $container->getExtensionConfig('ynlo_framework')[0];
        //enable ajax forms and assets
        $config['ajax_forms'] = true;
        $container->prependExtensionConfig('ynlo_framework', $config);
    }

    /**
     * {@inheritdoc}
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_modal_js', 'bundles/ynlomodal/js/modal.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_modal_css', 'bundles/ynlomodal/css/modals.css', ['yfp_config_dumper']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filterAssets(array $assets, array $config)
    {
        return $assets;
    }
}
