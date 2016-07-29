<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
 */

namespace YnloFramework\YnloPjaxBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

class YnloPjaxExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.pjax.config', $config);
        $container->setParameter('ynlo.js_plugin.pjax', [
            'target' => $config['target'],
            'links' => $config['links'],
            'forms' => $config['forms'],
            'autospin' => $config['autospin'],
            'spinicon' => $config['spinicon'],
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
        $config = $container->getExtensionConfig('ynlo_framework')[0];
        //enable ajax forms and assets
        $config['ajax_forms'] = true;

        $container->prependExtensionConfig('ynlo_framework', $config);
    }

    /**
     * @inheritDoc
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_pjax_js', 'bundles/ynlopjax/js/pjax.yfp.js', ['yfp_config_dumper'])
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterAssets(array $assets, array $config)
    {
        return $assets;
    }

}