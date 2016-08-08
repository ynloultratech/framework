<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegisterInterface;

/**
 * Read configuration.
 */
class YnloFormExtension extends Extension implements PrependExtensionInterface, AssetRegisterInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ynlo.form.confing', $config);

        $configDir = __DIR__.'/../Resources/config';
        $loader = new Loader\YamlFileLoader($container, new FileLocator($configDir));

        $loader->load('services.yml');

        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['DoctrineBundle'])) {
            $container->removeDefinition('rafrsr_datepicker_type_guesser');
            $container->removeDefinition('rafrsr_switchery_type_guesser');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        //automatically add required form fields
        $vendorConfig = $container->getExtensionConfig('twig')[0];

        //enabling our custom theme
        $vendorConfig['form_themes'][] = 'YnloFormBundle::fields.html.twig';
        $container->prependExtensionConfig('twig', $vendorConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function registerAssets(array $config, ContainerBuilder $containerBuilder)
    {
        $assets = [];
        //vendor
        $assets[] = AssetFactory::module('moment_js', 'bundles/ynloform/vendor/moment.min.js');

        if (array_key_value($config, 'datetimepicker')) {
            $assets[] = AssetFactory::module('bootstrap_datetimepicker_js', 'bundles/ynloform/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')
                ->setDependencies(['bootstrap', 'moment'])
                ->addJqueryPlugin('datetimepicker');
            $assets[] = AssetFactory::asset('bootstrap_datetimepicker_css', 'bundles/ynloform/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
            $assets[] = AssetFactory::asset('ynlo_form_date_time_picker_js', 'bundles/ynloform/js/form_date_time_picker.yfp.js', ['yfp_config_dumper']);
        }

        $assets[] = AssetFactory::module('spectrum_colorpicker_js', 'bundles/ynloform/vendor/spectrum/spectrum.js')
            ->addJqueryPlugin('spectrum');

        $assets[] = AssetFactory::asset('spectrum_colorpicker_css', 'bundles/ynloform/vendor/spectrum/spectrum.css');
        $assets[] = AssetFactory::asset('spectrum_colorpicker_theme_css', 'bundles/ynloform/vendor/spectrum/themes/bootstrap.css');

        //switchery does not work as module, why?
        $assets[] = AssetFactory::asset('switchery_js', 'bundles/ynloform/vendor/switchery/switchery.min.js');
        $assets[] = AssetFactory::asset('switchery_css', 'bundles/ynloform/vendor/switchery/switchery.min.css');


        if (array_key_value($config, 'select2.enabled')) {
            $assets[] = AssetFactory::module('select2_js', 'bundles/ynloform/vendor/select2/js/select2.full.min.js');
            $assets[] = AssetFactory::asset('select2_css', 'bundles/ynloform/vendor/select2/css/select2.min.css');
            if (array_key_value($config, 'select2.theme') === 'bootstrap') {
                $assets[] = AssetFactory::asset('select2_bootstrap_theme_css', 'bundles/ynloform/vendor/select2/css/select2-bootstrap.min.css');
            }
            $assets[] = AssetFactory::asset('ynlo_form_select2_js', 'bundles/ynloform/js/form_select2.yfp.js', ['yfp_config_dumper']);
        }

        $assets[] = AssetFactory::module('angular', 'bundles/ynloform/vendor/angular/angular.min.js');
        $assets[] = AssetFactory::module('angular_animate', 'bundles/ynloform/vendor/angular/angular-animate.min.js')
            ->setModuleName('ngAnimate')->setDependencies(['angular']);

        $assets[] = AssetFactory::module('bootstrap_typeahead_js', 'bundles/ynloform/vendor/bootstrap_typeahead/bootstrap3-typeahead.min.js')
            ->addJqueryPlugin('typeahead');
        $assets[] = AssetFactory::asset('typeahead_css', 'bundles/ynloform/vendor/typeahead/typeahead.css');

        $assets[] = AssetFactory::module('jquery_form_toggle', 'bundles/ynloform/vendor/jquery-form-toggle/jquery-form-toggle.js')
            ->addJqueryPlugin('formToggle');

        $assets[] = AssetFactory::asset('ynlo_form_js', 'bundles/ynloform/js/form.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_form_color_picker_js', 'bundles/ynloform/js/form_color_picker.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_form_switchery_js', 'bundles/ynloform/js/form_switchery.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_form_angular_controller_js', 'bundles/ynloform/js/form_angular_controller.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_form_typeahead_js', 'bundles/ynloform/js/form_typeahead.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_form_toggle_js', 'bundles/ynloform/js/form_toggle.yfp.js', ['yfp_config_dumper']);

        return $assets;
    }
}
