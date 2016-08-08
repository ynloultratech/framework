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
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegisterInterface;

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
    public function registerAssets(array $config, ContainerBuilder $containerBuilder)
    {
        $assets = [];
        //vendor
        $assets[] = AssetFactory::module('bootstrap_dialog_js', 'bundles/ynlomodal/vendor/bootstrap-dialog/bootstrap-dialog.min.js')
            ->setModuleName('bootstrap-dialog')->setDependencies(['bootstrap']);

        $assets[] = AssetFactory::asset('bootstrap_dialog_css', 'bundles/ynlomodal/vendor/bootstrap-dialog/bootstrap-dialog.min.css');

        //internal
        $assets[] = AssetFactory::asset('ynlo_modal_js', 'bundles/ynlomodal/js/modal.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_modal_css', 'bundles/ynlomodal/css/modals.css', ['yfp_config_dumper']);

        return $assets;
    }

    /**
     * {@inheritdoc}
     */
    public function filterAssets(array $assets, array $config)
    {
        return $assets;
    }
}
