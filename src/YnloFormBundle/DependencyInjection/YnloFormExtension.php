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
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

/**
 * Read configuration
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

        $configDir = __DIR__ . '/../Resources/config';
        $loader = new Loader\YamlFileLoader($container, new FileLocator($configDir));

        $loader->load('services.yml');

        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['DoctrineBundle'])) {
            $container->removeDefinition('rafrsr_datepicker_type_guesser');
            $container->removeDefinition('rafrsr_switchery_type_guesser');
        }
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_form_js', 'bundles/ynloform/js/form.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_date_time_picker_js', 'bundles/ynloform/js/form_date_time_picker.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_color_picker_js', 'bundles/ynloform/js/form_color_picker.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_switchery_js', 'bundles/ynloform/js/form_switchery.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_select2_js', 'bundles/ynloform/js/form_select2.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_angular_controller_js', 'bundles/ynloform/js/form_angular_controller.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_typeahead_js', 'bundles/ynloform/js/form_typeahead.yfp.js', ['yfp_config_dumper']),
            new AsseticAsset('ynlo_form_toggle_js', 'bundles/ynloform/js/form_toggle.yfp.js', ['yfp_config_dumper']),
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterAssets(array $assets, array $config)
    {
        if (array_key_value($config, 'datetimepicker.enabled') === false) {
            unset($assets['ynlo_form_date_time_picker_js'], $assets['moments_js'], $assets['bootstrap_datetimepicker_js'], $assets['bootstrap_datetimepicker_css']);
        }
        if (array_key_value($config, 'select2.enabled') === false) {
            unset($assets['ynlo_form_select2_js'], $assets['select2_js'], $assets['select2_css'], $assets['select2_bootstrap_theme_css']);
        } else {
            if (array_key_value($config, 'select2.theme') !== 'bootstrap') {
                unset($assets['select2_bootstrap_theme_css']);
            }
        }

        return $assets;
    }
}
