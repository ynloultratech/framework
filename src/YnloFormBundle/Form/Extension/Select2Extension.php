<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class Select2Extension
 */
class Select2Extension extends AbstractTypeExtension
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var array
     */
    private $formConfig = [];

    private $select2DefaultOptions
        = [
            'minimumResultsForSearch' => 20,
            'minimumInputLength' => 0,
            'theme' => 'bootstrap',
        ];

    /**
     * @param EngineInterface   $templating
     * @param RegistryInterface $doctrine
     * @param array             $formConfig array of settings of ynlo form bundle
     */
    public function __construct(EngineInterface $templating, RegistryInterface $doctrine = null, array $formConfig = [])
    {
        $this->templating = $templating;
        $this->doctrine = $doctrine;
        $this->formConfig = $formConfig;
    }

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ChoiceView $choice */
        foreach ($view->vars['choices'] as $choice) {
            if ($options['select2_template_result']) {

                $object = $choice->value;
                if ($this->doctrine && $options['class']) {
                    $object = $this->doctrine->getRepository($options['class'])->find($object);
                }
                if (is_string($options['select2_template_result'])) {
                    $choice->attr['data-template-result'] = $this->templating->render(
                        $options['select2_template_result'],
                        [
                            'choice' => $choice,
                            'object' => $object
                        ]
                    );
                } else {
                    $choice->attr['data-template-result']
                        = call_user_func_array($options['select2_template_result'], [$choice, $object]);
                }
            }

            if ($options['select2_template_selection']) {

                $object = $choice->value;
                if ($this->doctrine && $options['class']) {
                    $object = $this->doctrine->getRepository($options['class'])->find($object);
                }
                if (is_string($options['select2_template_selection'])) {
                    $choice->attr['data-template-selection'] = $this->templating->render(
                        $options['select2_template_selection'],
                        [
                            'choice' => $choice,
                            'object' => $object
                        ]
                    );
                } else {
                    $choice->attr['data-template-selection']
                        = call_user_func_array($options['select2_template_selection'], [$choice, $object]);
                }
            }
        }

        if ($options['select2'] === true) {
            if ($options['autocomplete']) {
                $options['select2_options']['minimumResultsForSearch'] = 0;
                if (!isset($options['select2_options']['minimumInputLength'])) {
                    $options['select2_options']['minimumInputLength'] = $options['autocomplete_min_length'];
                }
            }
            $this->select2DefaultOptions['theme'] = array_key_value($this->formConfig, 'select2.theme', $this->select2DefaultOptions['theme']);
            $options['select2_options'] = array_merge($this->select2DefaultOptions, $options['select2_options']);

            $view->vars['attr']['select2'] = null;
            $view->vars['attr']['select2-options'] = json_encode($options['select2_options']);
        }
    }

    /**
     * @inheritdoc
     *
     * @throws AccessException
     * @throws UndefinedOptionsException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'select2' => true,
                'select2_options' => [],
                'select2_template_result' => null,
                'select2_template_selection' => null,
            ]
        );
        $resolver->setAllowedTypes('select2', ['boolean']);
        $resolver->setAllowedTypes('select2_options', ['null', 'array']);
        $resolver->setAllowedTypes('select2_template_result', ['null', 'string', 'callable']);
        $resolver->setAllowedTypes('select2_template_selection', ['null', 'string', 'callable']);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }
}