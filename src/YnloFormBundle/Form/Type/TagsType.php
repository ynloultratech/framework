<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagType
 */
class TagsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['value'] = $view->vars['data'];
        if (is_array($form->getNormData())) {
            $view->vars['choices'] = [];
            foreach ($form->getNormData() as $tag) {
                $view->vars['choices'][] = new ChoiceView($tag, $tag, $tag);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $form = $event->getForm();
                $data = (array)$event->getData();
                foreach ($data as $tag) {
                    $options['choices'][$tag] = $tag;
                    $options['data'][] = $tag;
                }
                $form->getParent()->add($form->getName(), ChoiceType::class, $options);
                $form->getParent()->get($form->getName())->submit($data);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'multiple' => true,
                'select2_options' => [
                    'tags' => true,
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
