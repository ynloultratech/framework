<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeaheadExtension extends AutocompleteBaseExtension
{
    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);

        if ($options['autocomplete']) {
            $view->vars['attr']['typeahead'] = null;
            if (is_array($options['autocomplete'])) {
                $view->vars['attr']['typeahead-source'] = json_encode($options['autocomplete']);
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function buildAutocompleteContext(array $options)
    {
        $context = parent::buildAutocompleteContext($options);
        $context->setProvider('typeahead');

        return $context;
    }

    /**
     * @inheritdoc
     *
     * @throws AccessException
     * @throws UndefinedOptionsException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('autocomplete_min_length', 1);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return TextType::class;
    }
}