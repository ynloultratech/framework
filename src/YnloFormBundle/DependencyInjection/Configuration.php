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

namespace YnloFramework\YnloFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetConfiguration;

/**
 * This is the class that validates and merges configuration files
 */
class Configuration implements ConfigurationInterface
{
    use AssetConfiguration;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ynlo_form')->children();

        $rootNode->booleanNode('datetimepicker')->defaultValue(false);
        $rootNode->arrayNode('select2')->canBeEnabled()
            ->children()
            ->scalarNode('theme')->defaultValue('bootstrap');

        $this->createAssetConfig(
            $rootNode, [
                'moments_js' => 'bundles/ynloform/vendor/moment.min.js',
                'bootstrap_datetimepicker_js' => 'bundles/ynloform/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js',
                'bootstrap_datetimepicker_css' => 'bundles/ynloform/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
                'spectrum_colorpicker_js' => 'bundles/ynloform/vendor/spectrum/spectrum.js',
                'spectrum_colorpicker_css' => 'bundles/ynloform/vendor/spectrum/spectrum.css',
                'spectrum_colorpicker_theme_css' => 'bundles/ynloform/vendor/spectrum/themes/bootstrap.css',
                'switchery_js' => 'bundles/ynloform/vendor/switchery/switchery.min.js',
                'switchery_css' => 'bundles/ynloform/vendor/switchery/switchery.min.css',
                'select2_js' => 'bundles/ynloform/vendor/select2/js/select2.full.min.js',
                'select2_css' => 'bundles/ynloform/vendor/select2/css/select2.min.css',
                'select2_bootstrap_theme_css' => 'bundles/ynloform/vendor/select2/css/select2-bootstrap.min.css',
                'angular_js' => 'bundles/ynloform/vendor/angular/angular.min.js',
                'angular_animate_js' => 'bundles/ynloform/vendor/angular/angular-animate.min.js',
                'typeahead_js' => 'bundles/ynloform/vendor/typeahead/typeahead.bundle.min.js',
                'typeahead_css' => 'bundles/ynloform/vendor/typeahead/typeahead.css',
                'jquery_form_toggle' => 'bundles/ynloform/vendor/jquery-form-toggle/jquery-form-toggle.js',
            ]
        );

        return $treeBuilder;
    }
}
