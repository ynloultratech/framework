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
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegisterInterface;

/**
 * YnloFrameworkExtension.
 */
class YnloFrameworkExtension extends Extension implements AssetRegisterInterface
{
    /**
     * {@inheritdoc}
     */
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
    public function registerAssets(array $config, ContainerBuilder $containerBuilder)
    {
        $assets = [];
        $assets[] = AssetFactory::asset('jquery', 'bundles/ynloframework/vendor/jquery/jquery.min.js');
        $assets[] = AssetFactory::module('underscore', 'bundles/ynloframework/vendor/underscore/underscore-min.js');

        if ($config['ajax_forms']) {
            $assets[] = AssetFactory::module('jquery_form', 'bundles/ynloframework/vendor/jquery.form/jquery.form.js');
        }

        if ($config['pace']) {
            $assets[] = AssetFactory::module('pace_js', 'bundles/ynloframework/vendor/pace/pace.js');
            $assets[] = AssetFactory::asset('pace_config', 'bundles/ynloframework/js/pace_settings.js', ['pace_settings_dumper']);
            $assets[] = AssetFactory::asset('pace_css', 'bundles/ynloframework/vendor/pace/pace.css');
        }

        //$assets[] = AssetFactory::asset('fastclick', 'bundles/ynloframework/vendor/fastclick/fastclick.js');
        $assets[] = AssetFactory::module('bootstrap_js', 'bundles/ynloframework/vendor/bootstrap/js/bootstrap.min.js')
            ->setCdn('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
        $assets[] = AssetFactory::asset('bootstrap_css', 'bundles/ynloframework/vendor/bootstrap/css/bootstrap.min.css');
        $assets[] = AssetFactory::asset('fontawesome', 'bundles/ynloframework/vendor/font-awesome/css/font-awesome.min.css');
        $assets[] = AssetFactory::asset('glyphicons', 'bundles/ynloframework/vendor/glyphicons/css/glyphicons.css');
        $assets[] = AssetFactory::asset('icomoon', 'bundles/ynloframework/vendor/icomoon/icomoon.css');
        $assets[] = AssetFactory::asset('ynlo_framework_js', 'bundles/ynloframework/js/framework.js');
        $assets[] = AssetFactory::asset('ynlo_framework_core_js', 'bundles/ynloframework/js/core.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_framework_libraries_js', 'bundles/ynloframework/js/lib/*.js');

        if ($config['debug']) {
            $assets[] = AssetFactory::asset('ynlo_debugger_js', 'bundles/ynloframework/js/debugger.yfp.js', ['yfp_config_dumper']);
            $assets[] = AssetFactory::asset('ynlo_debugger_css', 'bundles/ynloframework/css/debugger.css');
        }

        if ($config['animate_css']) {
            $assets[] = AssetFactory::asset('animate_css', 'bundles/ynloframework/vendor/animate/animate.min.css');
        }

        return $assets;
    }
}
