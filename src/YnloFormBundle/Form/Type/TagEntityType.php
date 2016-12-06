<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class TagEntityType extends AbstractType
{
    protected $propertyAccessor;
    protected $em;

    public function __construct(PropertyAccessorInterface $propertyAccessor, EntityManagerInterface $em)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $preSetDataListener = function (FormEvent $event) use ($options) {
            if (null === $options['choice_loader'] || !$event->getData() instanceof \Traversable) {
                return;
            }

            $form = $event->getForm();
            $data = $event->getData() ?: [];

            $options['choices'] = $data;
            $options['choice_loader'] = null;
            $form->getParent()->add($form->getName(), __CLASS__, $options);
        };

        $newItems = [];
        $preSubmitListener = function (FormEvent $event) use ($options, &$newItems) {
            $data = $event->getData();
            $viewData = $event->getForm()->getViewData();

            // exit if they aren't new items
            if (null === $data || 0 === count($diff = array_diff($data, $viewData))) {
                return;
            }

            // create new items and save them
            foreach ($diff as &$value) {
                $item = new $options['class']();
                $this->propertyAccessor->setValue($item, $options['property'], $value);
                $newItems[] = $item;
            }

            // remove new items from data to avoid validation errors
            foreach ($diff as $key => $item) {
                unset($data[$key]);
            }

            // update data
            $event->setData($data);
        };

        $postSubmitListener = function (FormEvent $event) use ($options, &$newItems) {
            if (0 === count($newItems)) {
                return;
            }

            $parent = $event->getForm()->getParent()->getData();

            // add new items to parent object
            foreach ($newItems as $item) {
                $parent->{$options['add_method']}($item);
                $this->em->persist($item);
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $preSetDataListener);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $preSubmitListener);
        $builder->addEventListener(FormEvents::POST_SUBMIT, $postSubmitListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['add_method', 'property']);
        $resolver->setDefaults([
            'multiple' => true,
            'select2_options' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
