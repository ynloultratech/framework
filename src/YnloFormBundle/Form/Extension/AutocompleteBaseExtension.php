<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Doctrine\ORM\QueryBuilder;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContext;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AutocompleteBaseExtension extends AbstractTypeExtension implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['autocomplete']) {

            $context = $this->buildAutocompleteContext($options);

            $contextId = $this->container->get('ynlo.form.autocomplete.context_manager')->addContext($context);

            $options['autocomplete_route_parameters']['_context'] = $contextId;
            if (!$options['autocomplete_url']) {
                $url = $this->container->get('router')->generate($options['autocomplete_route'], $options['autocomplete_route_parameters']);
                $view->vars['attr']['autocomplete-url'] = $url;
            }

            if ($options['autocomplete_related_fields']) {
                $view->vars['attr']['autocomplete-related-fields'] = json_encode($options['autocomplete_related_fields']);
            }
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
                'autocomplete' => null,
                'autocomplete_related_fields' => [],
                'autocomplete_min_length' => 0,
                'autocomplete_url' => null,
                'autocomplete_route' => 'ynlo_form_autocomplete',
                'autocomplete_route_parameters' => [],
                'autocomplete_max_results' => 20,
                'autocomplete_order_by' => null,
                'class' => null,
                'query_builder' => null,
            ]
        );

        $resolver->setAllowedTypes('autocomplete', ['null', 'string', 'array']);
        $resolver->setAllowedTypes('autocomplete_related_fields', ['null', 'array']);
        $resolver->setAllowedTypes('autocomplete_min_length', ['integer']);
        $resolver->setAllowedTypes('autocomplete_url', ['null', 'string']);
        $resolver->setAllowedTypes('autocomplete_route', ['string']);
        $resolver->setAllowedTypes('autocomplete_route_parameters', ['array']);
        $resolver->setAllowedTypes('autocomplete_max_results', ['integer']);
        $resolver->setAllowedTypes('autocomplete_order_by', ['null', 'array']);
        $resolver->setAllowedTypes('class', ['null', 'string']);
        $resolver->setAllowedTypes('query_builder', ['null', 'callable', 'Doctrine\ORM\QueryBuilder']);
    }

    /**
     * buildAutocompleteContext
     *
     * @param array $options
     *
     * @return AutocompleteContext
     */
    protected function buildAutocompleteContext(array $options)
    {
        $context = new AutocompleteContext();
        $context->setParameter('class', $options['class']);
        $context->setParameter('autocomplete', $options['autocomplete']);
        $context->setParameter('order_by', $options['autocomplete_order_by']);
        $context->setParameter('max_results', $options['autocomplete_max_results']);

        $qb = null;
        if (isset($options['query_builder'])) {
            $qb = $options['query_builder'];
        } elseif (isset($options['query'])) { //support for sonata ModelType
            $qb = $options['query'];
        }
        if ($qb instanceof QueryBuilder) {
            $context->setParameter('dql_parts', $qb->getDQLParts());
        }

        return $context;
    }
}