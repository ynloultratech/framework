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

namespace YnloFramework\YnloFormBundle\Form\Type;

use YnloFramework\YnloFormBundle\Form\Transformer\DateTimePickerTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimePickerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new DateTimePickerTransformer($this->getIntlDateFormatter($options)));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $widgetOptions = $this->parseWidgetOptions(
            array_intersect_key($options, $this->getDefaultWidgetOptions())
        );

        // allow custom format (e.g 'MM/YYYY')
        if (isset($options['format'])) {
            $format = $options['format'];
        } else {
            $format = $this->getIntlDateFormatter($options)->getPattern();
        }

        $widgetOptions['format'] = $this->momentJSFormatConvert($format);

        $view->vars['attr']['date-picker'] = null;
        if (isset($view->vars['widget_form_control_class']) && isset($view->vars['widget_addon_append']['icon'])) {
            $view->vars['attr']['date-picker-style'] = 'bootstrap';
        } else {
            $view->vars['attr']['date-picker-style'] = 'default';
        }

        $view->vars['type'] = 'text';
        $view->vars['attr']['date-picker-options'] = json_encode($widgetOptions);
    }

    /**
     * Parse widget options.
     *
     * @param array $options
     *
     * @return array
     */
    public function parseWidgetOptions(array $options)
    {
        //remove null settings
        $options = array_filter(
            $options,
            function ($val) {
                return $val !== null;
            }
        );

        $parsedOptions = [];
        foreach ($options as $key => $value) {
            if (false !== strpos($key, 'dp_')) {
                // remove 'dp_' prefix
                $dpKey = substr($key, 3);

                $parsedOptions[$dpKey] = $value;
            }
        }

        return $parsedOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array_merge(
                $this->getDefaultWidgetOptions(),
                [
                    'widget' => 'single_text',
                    'format' => null,
                    'date_format' => \IntlDateFormatter::MEDIUM,
                    'time_format' => \IntlDateFormatter::SHORT,
                    'widget_addon_append' => [
                        'icon' => 'calendar',
                    ],
                ]
            )
        );

        $resolver->setAllowedTypes('format', ['null', 'string']);

        $resolver->setAllowedValues(
            'date_format',
            [
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::SHORT,
            ]
        );

        $resolver->setAllowedValues(
            'time_format',
            [
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::LONG,
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::SHORT,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ynlo_date_time_picker';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * Get default datetime picker options.
     *
     * @return array
     */
    protected function getDefaultWidgetOptions()
    {
        return [
            'dp_locale' => \Locale::getDefault(),
            'dp_dayViewHeaderFormat' => null,
            'dp_extraFormats' => null,
            'dp_stepping' => null,
            'dp_minDate' => null,
            'dp_maxDate' => null,
            'dp_collapse' => null,
            'dp_defaultDate' => null,
            'dp_disabledDates' => null,
            'dp_enabledDates' => null,
            'dp_icons' => null,
            'dp_useStrict' => null,
            'dp_viewMode' => null,
            'dp_daysOfWeekDisabled' => null,
            'dp_calendarWeeks' => null,
            'dp_toolbarPlacement' => null,
            'dp_showTodayButton' => null,
            'dp_showClear' => null,
            'dp_showClose' => null,
            'dp_widgetParent' => null,
            'dp_keepOpen' => null,
            'dp_keepInvalid' => null,
            'dp_debug' => null,
            'dp_ignoreReadonly' => null,
            'dp_disabledTimeIntervals' => null,
            'dp_allowInputToggle' => true,
            'dp_focusOnShow' => null,
            'dp_enabledHours' => null,
            'dp_disabledHours' => null,
            'dp_viewDate' => null,
            'dp_tooltips' => null,
            'dp_inline' => null,
            'dp_sideBySide' => null,
        ];
    }

    /**
     * Returns associated moment.js format.
     *
     * @param string $format PHP Date format
     *
     * @return string Moment.js date format
     */
    private function momentJSFormatConvert($format)
    {
        $formatConvertRules = [
            // year
            'yyyy' => 'YYYY',
            'yy' => 'YY',
            'y' => 'YYYY',
            // month
            'M' => 'M',
            'LLL' => 'MMM',
            'LLLL' => 'MMMM',
            // day
            'dd' => 'DD',
            'd' => 'D',
            // hour
            'HH' => 'HH',
            'H' => 'H',
            'h' => 'h',
            'hh' => 'hh',
            // am/pm
            'a' => 'a',
            // minute
            'mm' => 'mm',
            'm' => 'm',
            // second
            'ss' => 'ss',
            's' => 's',
            // day of week
            'EE' => 'ddd',
            'EEEE' => 'dddd',
            // timezone
            'ZZZZZ' => 'Z',
            'ZZZ' => 'ZZ',
            // letter 'T'
            'T' => 'T',
        ];

        return strtr($format, $formatConvertRules);
    }

    /**
     * Get IntlDateFormatter instance from data_format and time_format options.
     *
     * @param array $options
     *
     * @return \IntlDateFormatter
     */
    private function getIntlDateFormatter(array $options)
    {
        return new \IntlDateFormatter(\Locale::getDefault(), $options['date_format'], $options['time_format']);
    }
}
