<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2AutocompleteExtension extends AutocompleteBaseExtension
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                if ($options['autocomplete']) {
                    /** @var FormInterface $form */
                    $form = $event->getForm();
                    $data = $event->getData();

                    if (!array_key_exists('em', $options)) {
                        return;
                    }

                    $idReader = $options['id_reader'];
                    $em = $options['em'];

                    $items = $em->getRepository($options['class'])->findBy([$idReader->getIdField() => $data]);
                    foreach ($items as $item) {
                        $options['choices'][$idReader->getIdValue($item)] = $item;
                    }

                    if ($options['multiple']) {
                        $options['data'] = $options['choices'];
                    } else {
                        $options['data'] = current($options['choices']);
                    }

                    $type = get_class($form->getConfig()->getType()->getInnerType());
                    $form->getParent()->add($form->getName(), $type, $options);
                }
            }
        );
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

        $choiceNormalizer = function (OptionsResolver $options, $prevValue) {
            if ($prevValue === null && $options['autocomplete']) {
                return [];
            }

            return $prevValue;
        };

        $resolver->setNormalizer('choices', $choiceNormalizer);
    }

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }

    /**
     * @inheritDoc
     */
    protected function buildAutocompleteContext(array $options)
    {
        $context = parent::buildAutocompleteContext($options);
        if (isset($options['select2_options']['tags']) && $options['select2_options']['tags']) {
            $context->setProvider('select2_tags');
        } else {
            $context->setProvider('select2');
        }

        $context->setParameter('select2_template_result', $options['select2_template_result']);
        $context->setParameter('select2_template_selection', $options['select2_template_selection']);

        return $context;
    }
}