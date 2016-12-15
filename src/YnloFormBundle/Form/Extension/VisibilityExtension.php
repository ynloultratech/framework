<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VisibilityExtension extends AbstractTypeExtension
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $options = $form->getConfig()->getOptions();

            if (false === $options['visibility'] && $form->getParent()) {
                $form->getParent()->remove($form->getName());
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $normalizer = function (Options $options, $value) {
            if (is_bool($value)) {
                return $value;
            }

            // sonata checker support: 'CREATE', 'EDIT', etc.
            if ($options->offsetExists('sonata_field_description') && null !== $admin = $options['sonata_field_description']->getAdmin()) {
                /* @var AbstractAdmin $admin */
                return $admin->isGranted($value);
            }

            return $this->authorizationChecker->isGranted($value);
        };

        $resolver
            ->setDefaults(['visibility' => true])
            // e.g conditional or 'ROLE_A' or ['ROLE_A', 'ROLE_B']
            ->setAllowedTypes('visibility', ['bool', 'string', 'array'])
            ->setNormalizer('visibility', $normalizer)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
