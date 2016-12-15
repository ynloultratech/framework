<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Component\Form\ChoiceList\Factory\CachingFactoryDecorator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2AutocompleteExtension extends AutocompleteBaseExtension
{
    private $idReaders;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['autocomplete']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
        }
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();

        if (!$event->getData() || $options['choices']) {
            return;
        }

        $data = $event->getData();
        $options['choices'] = is_array($data) || $data instanceof \Traversable ? $data : [$data];

        $type = get_class($form->getConfig()->getType()->getInnerType());
        $form->getParent()->add($form->getName(), $type, $options);
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();

        if ($options['_autocomplete_submitted'] && isset($options['em'])) {
            return;
        }

        $data = $event->getData();
        $options['choices'] = $options['em']->getRepository($options['class'])->findBy([
            $options['id_reader']->getIdField() => $data,
        ]);
        $options['data'] = $options['multiple'] ? $options['choices'] : current($options['choices']);
        $options['_autocomplete_submitted'] = true;

        $type = get_class($form->getConfig()->getType()->getInnerType());
        $form->getParent()->add($form->getName(), $type, $options);
        $form->getParent()->get($form->getName())->submit($data);
    }

    /**
     * {@inheritdoc}
     *
     * @throws AccessException
     * @throws UndefinedOptionsException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $choiceNormalizer = function (OptionsResolver $options, $value) {
            return $value === null && $options['autocomplete'] ? [] : $value;
        };

        $emNormalizer = function (Options $options, $em) {
            //support for sonata model type
            if (null === $em && !empty($options['model_manager']) && !empty($options['class'])) {
                $em = $options['model_manager']->getEntityManager($options['class']);
            }

            return $em;
        };

        $queryBuilderNormalizer = function (Options $options, $queryBuilder) {
            if (is_callable($queryBuilder) && !empty($options['em'])) {
                $queryBuilder = call_user_func($queryBuilder, $options['em']->getRepository($options['class']));
            }

            return $queryBuilder;
        };

        // Set the "id_reader" option via the normalizer. This option is not
        // supposed to be set by the user.
        $idReaderNormalizer = function (Options $options, $idReader) {
            if (empty($options['em']) || $idReader instanceof IdReader) {
                return $idReader;
            }

            $hash = CachingFactoryDecorator::generateHash([
                $options['em'],
                $options['class'],
            ]);

            if (!isset($this->idReaders[$hash])) {
                $classMetadata = $options['em']->getClassMetadata($options['class']);
                $this->idReaders[$hash] = new IdReader($options['em'], $classMetadata);
            }

            return $this->idReaders[$hash];
        };

        //this option is used internally to know when the autocomplete
        //has been initialized in the pre-submit data
        $resolver->setDefault('_autocomplete_submitted', false);
        $resolver->setDefault('em', null);
        $resolver->setDefault('id_reader', null); // internal

        $resolver->setAllowedTypes('em', ['null', 'Doctrine\ORM\EntityManager']);

        $resolver->setNormalizer('em', $emNormalizer);
        $resolver->setNormalizer('query_builder', $queryBuilderNormalizer);
        $resolver->setNormalizer('id_reader', $idReaderNormalizer);
        $resolver->setNormalizer('choices', $choiceNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
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
