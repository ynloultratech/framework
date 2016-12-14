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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use YnloFramework\Component\FileReader\Matcher\ColumnMatcher;

class ColumnMatcherType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ColumnMatcher $matcher */
        $matcher = $options['matcher'];
        $matcher->clearReaderColumns();

        $choices = [];
        foreach ($matcher->getColumns() as $column) {
            $choices[$column->getLabel()] = $column->getName();
        }

        foreach ($matcher->getPreviewColumns() as $index => $col) {
            $builder->add("index_$index", ChoiceType::class, [
                'placeholder' => 'Ignore This',
                'label' => false,
                'mapped' => false,
                'required' => false,
                'choices' => $choices,
                'data' => $matcher->getPreselectedData($index),
                'choice_attr' => function ($val, $key, $index) use ($matcher) {
                    return [
                        'data-required' => $matcher->getColumns()->get($index)->isRequired() ? 'true' : false,
                        'data-restricted' => $matcher->getColumns()->get($index)->isRestricted() ? 'true' : false,
                    ];
                },
            ]);

            $builder
                ->add("index_restricted_$index", CheckboxType::class, [
                    'label' => 'Restricted',
                    'data' => $matcher->isRestrictedColumn($index),
                    'mapped' => false,
                    'required' => false,
                ]);
        }

        $postSubmitColumnListener = function (FormEvent $event) use ($matcher) {
            $form = $event->getForm();
            $index = 0;
            $cols = [];
            while ($form->has("index_$index")) {
                $cols[$index] = $form->get("index_$index")->getData();

                if ($column = $form->get("index_$index")->getData()) {
                    $matcher->getColumns()->get($column)->setIndex($index);
                }

                if ($form->get("index_restricted_$index")->getData()) {
                    $matcher->addRestrictedColumn($index);
                }

                ++$index;
            }

            $selectedColumns = array_count_values(array_filter($cols));
            foreach ($selectedColumns as $column => $count) {
                if ($count > 1) {
                    $error = sprintf(
                        'The column %s has been selected more than once',
                        $matcher->getColumns()->get($column)->getLabel()
                    );
                    $form->addError(new FormError($error));
                }
            }

            foreach ($matcher->getColumns() as $column) {
                if ($column->isRequired() && !isset($selectedColumns[$column->getName()])) {
                    $error = sprintf(
                        'The column %s is required.',
                        $column->getLabel()
                    );
                    $form->addError(new FormError($error));
                }
            }

            if (0 === $form->getParent()->getErrors()->count()) {
                $matcher->updateReaderColumns();
            }
        };

        $builder->addEventListener(FormEvents::POST_SUBMIT, $postSubmitColumnListener);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ColumnMatcher $matcher */
        $matcher = $options['matcher'];
        $view->vars['previewColumns'] = $matcher->getPreviewColumns();
        $view->vars['previewData'] = $matcher->getPreviewData($options['previewSize']);
        $view->vars['show_restricted'] = $options['show_restricted'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'matcher' => null,
                'previewSize' => 10,
                'error_bubbling' => true,
                'show_restricted' => false,
            ])
            ->setAllowedTypes('matcher', [ColumnMatcher::class])
            ->setAllowedTypes('previewSize', ['int'])
            ->setRequired(['matcher']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'column_matcher';
    }
}
