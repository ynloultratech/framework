<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

class EmbeddedTemplateType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // this field will not be a real property of any class
        $builder->setMapped(false);
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['parameters']['form'] = $view;
        $view->vars['template'] = $this->templating->render($options['template'], $options['parameters']);

        $options['required'] = false;
        if ($options['labeled'] !== true) {
            // the label will not be displayed
            $view->vars['label'] = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'parameters' => [],
                    'labeled' => false,
                    'required' => false,
                ]
            )
            ->setRequired(['template'])
            ->setAllowedTypes('template', 'string')
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('labeled', 'bool');
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ynlo_embedded_template';
    }
}