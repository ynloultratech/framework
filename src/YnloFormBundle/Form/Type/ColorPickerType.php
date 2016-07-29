<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColorPickerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $widgetOptions = array_intersect_key($form->getConfig()->getOptions(), $this->getDefaultWidgetOptions());
        $widgetOptions['cp_color'] = $view->vars['value'] ?: $widgetOptions['cp_color'];
        $widgetOptions = $this->parseWidgetOptions($widgetOptions);

        if (isset($widgetOptions['cp_color'])) {
            $view->vars['value'] = $widgetOptions['cp_color'];
        }

        $view->vars['type'] = 'text';
        $view->vars['attr']['color-picker'] = null;
        $view->vars['attr']['color-picker-options'] = json_encode($widgetOptions);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ynlo_color_picker';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->getDefaultWidgetOptions());
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * parseWidgetOptions
     *
     * @param $options
     *
     * @return array
     */
    public function parseWidgetOptions($options)
    {
        //remove null settings
        $options = array_filter(
            $options,
            function ($val) {
                return ($val !== null);
            }
        );

        $parsedOptions = [];
        foreach ($options as $key => $value) {
            if (false !== strpos($key, 'cp_')) {
                // remove 'dp_' prefix
                $dpKey = substr($key, 3);

                $parsedOptions[$dpKey] = $value;
            }
        }

        return $parsedOptions;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultWidgetOptions()
    {
        return [
            'cp_color' => null,
            'cp_flat' => false,
            'cp_showInput' => null,
            'cp_allowEmpty' => false,
            'cp_showInitial' => true,
            'cp_showAlpha' => null,
            'cp_disabled' => null,
            'cp_localStorageKey' => null,
            'cp_showPalette' => true,
            'cp_showPaletteOnly' => null,
            'cp_togglePaletteOnly' => null,
            'cp_showSelectionPalette' => null,
            'cp_clickoutFiresChange' => true,
            'cp_cancelText' => null,
            'cp_chooseText' => null,
            'cp_togglePaletteMoreText' => null,
            'cp_togglePaletteLessText' => null,
            'cp_containerClassName' => null,
            'cp_replacerClassName' => null,
            'cp_preferredFormat' => 'hex',
            'cp_maxSelectionSize' => null,
            'cp_palette' => [
                ['#000', '#444', '#666', '#999', '#ccc', '#eee', '#f3f3f3', '#fff'],
                ['#f00', '#f90', '#ff0', '#0f0', '#0ff', '#00f', '#90f', '#f0f'],
                ['#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d0e0e3', '#cfe2f3', '#d9d2e9', '#ead1dc'],
                ['#ea9999', '#f9cb9c', '#ffe599', '#b6d7a8', '#a2c4c9', '#9fc5e8', '#b4a7d6', '#d5a6bd'],
                ['#e06666', '#f6b26b', '#ffd966', '#93c47d', '#76a5af', '#6fa8dc', '#8e7cc3', '#c27ba0'],
                ['#c00', '#e69138', '#f1c232', '#6aa84f', '#45818e', '#3d85c6', '#674ea7', '#a64d79'],
                ['#900', '#b45f06', '#bf9000', '#38761d', '#134f5c', '#0b5394', '#351c75', '#741b47'],
                ['#600', '#783f04', '#7f6000', '#274e13', '#0c343d', '#073763', '#20124d', '#4c1130']
            ],
            'cp_selectionPalette' => null,
        ];
    }
}