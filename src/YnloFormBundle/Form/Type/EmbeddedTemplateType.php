<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // this field will not be a real property of any class
        $builder->setMapped(false);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['parameters']['form'] = $view;
        $view->vars['template'] = $options['template'];
        $view->vars['parameters'] = $options['parameters'];

        $options['required'] = false;
        if ($options['labeled'] !== true) {
            // the label will not be displayed
            $view->vars['label'] = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'parameters' => [],
                'labeled' => false,
                'required' => false,
            ])
            ->setRequired(['template'])
            ->setAllowedTypes('template', 'string')
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('labeled', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ynlo_embedded_template';
    }
}
