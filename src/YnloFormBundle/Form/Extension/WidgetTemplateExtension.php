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
use Symfony\Component\Templating\EngineInterface;

/**
 * Class WidgetTemplateExtension.
 */
class WidgetTemplateExtension extends AbstractTypeExtension
{
    private $defaults
        = [
            'template_append' => null,
            'template_prepend' => null,
            'template_parameters' => [],
            'input_wrapper_class' => 'col-md-12',
            'append_clearfix' => false,
            'prepend_clearfix' => false,
            'append_separator' => false,
            'prepend_separator' => false,
        ];

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $options['template_parameters'] = array_merge(['form' => $view], $options['template_parameters']);

        foreach (array_keys($this->defaults) as $key) {
            $view->vars[$key] = $options[$key];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->defaults);
        $resolver->setAllowedTypes('template_append', ['null', 'string']);
        $resolver->setAllowedTypes('template_prepend', ['null', 'string']);
        $resolver->setAllowedTypes('template_parameters', ['array']);
        $resolver->setAllowedTypes('input_wrapper_class', ['null', 'string']);
        $resolver->setAllowedTypes('append_clearfix', ['bool']);
        $resolver->setAllowedTypes('prepend_clearfix', ['bool']);
        $resolver->setAllowedTypes('append_separator', ['bool']);
        $resolver->setAllowedTypes('prepend_separator', ['bool']);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
