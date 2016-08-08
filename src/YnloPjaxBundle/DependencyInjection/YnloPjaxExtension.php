<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloPjaxBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegisterInterface;

class YnloPjaxExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.pjax.config', $config);
        $container->setParameter(
            'ynlo.js_plugin.pjax',
            [
                'target' => $config['target'],
                'links' => $config['links'],
                'forms' => $config['forms'],
                'autospin' => $config['autospin'],
                'spinicon' => $config['spinicon'],
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
    public function registerAssets(array $config, ContainerBuilder $containerBuilder)
    {
        return [
            AssetFactory::asset('ynlo_pjax_js', 'bundles/ynlopjax/js/pjax.yfp.js', ['yfp_config_dumper']),
        ];
    }
}
