<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class WidgetTemplateExtension
 */
class WidgetTemplateExtension extends AbstractTypeExtension
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $options['template_parameters'] = array_merge(['form' => $view], $options['template_parameters']);
        if (isset($options['template_append'])) {
            $view->vars['template_append'] = $this->templating->render(
                $options['template_append'],
                $options['template_parameters']
            );
        }

        if (isset($options['template_prepend'])) {
            $view->vars['template_prepend'] = $this->templating->render(
                $options['template_prepend'],
                $options['template_parameters']
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'template_append' => null,
                'template_prepend' => null,
                'template_parameters' => [],
            ]
        );
        $resolver->setAllowedTypes('template_append', ['null', 'string']);
        $resolver->setAllowedTypes('template_prepend', ['null', 'string']);
        $resolver->setAllowedTypes('template_parameters', ['array']);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}