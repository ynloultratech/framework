<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SwitcheryType.
 */
class SwitcheryType extends AbstractType
{
    protected $widgetOptions
        = [
            'sw_color' => null,
            'sw_secondaryColor' => null,
            'sw_jackColor' => null,
            'sw_jackSecondaryColor' => null,
            'sw_className' => null,
            'sw_disabled' => null,
            'sw_disabledOpacity' => null,
            'sw_speed' => null,
            'sw_size' => null,
        ];

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $widgetOptions = $this->parseWidgetOptions(array_intersect_key($options, $this->widgetOptions));
        $view->vars['attr']['switchery'] = null;
        $view->vars['attr']['switchery-options'] = json_encode($widgetOptions);

        parent::buildView($view, $form, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            array_merge(
                [
                    'required' => false,
                ],
                $this->widgetOptions
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CheckboxType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ynlo_switchery';
    }

    /**
     * parseWidgetOptions.
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
                return $val !== null;
            }
        );

        $parsedOptions = [];
        foreach ($options as $key => $value) {
            if (false !== strpos($key, 'sw_')) {
                // remove 'sw_' prefix
                $dpKey = substr($key, 3);

                $parsedOptions[$dpKey] = $value;
            }
        }

        return $parsedOptions;
    }
}
