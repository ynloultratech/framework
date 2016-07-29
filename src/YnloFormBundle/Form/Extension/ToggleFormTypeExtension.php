<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToggleFormTypeExtension extends AbstractTypeExtension
{
    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        //TODO: support for expanded choices (radio buttons)

        if (isset($options['toggle_group'])) {
            $groups = (array)$options['toggle_group'];

            if ($groups) {
                $view->vars['toggle_groups'] = $groups;
            }
        }

        if (isset($options['toggle'])
            || isset($options['toggle_prefix'])
        ) {
            $view->vars['attr']['form-toggle'] = null;
            $toggleOptions = [];
            if (isset($view->vars['toggle_reverse_prefix'])) {
                $toggleOptions = [
                    'dataAttribute' => 'reverse-toggle',
                    'reverse' => true
                ];
            }
            $view->vars['attr']['form-toggle-options'] = json_encode($toggleOptions);

            //convert into class name
            if ($options['toggle']) {
                $view->vars['attr']['data-toggle'] = '.toggle_group_' . $options['toggle'];
            }

            if ($options['toggle_prefix']) {
                $view->vars['attr']['data-toggle-prefix'] = '.toggle_group_' . $options['toggle_prefix'];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'toggle' => null,
                'toggle_prefix' => null,
                'toggle_reverse_prefix' => null,
                'toggle_group' => null,
            ]
        );

        $resolver->setAllowedTypes('toggle', ['null', 'string']);
        $resolver->setAllowedTypes('toggle_prefix', ['null', 'string']);
        $resolver->setAllowedTypes('toggle_reverse_prefix', ['null', 'string']);
        $resolver->setAllowedTypes('toggle_group', ['null', 'string', 'array']);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}