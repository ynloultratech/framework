<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\PersistentCollection;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use YnloFramework\YnloAdminBundle\Admin\AbstractAdmin;

class EmbeddedAdminType extends AbstractType
{
    protected $adminPool;
    protected $registry;

    public function __construct(Pool $adminPool, ManagerRegistry $registry)
    {
        $this->adminPool = $adminPool;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['admin_code'])) {
            $code = $options['admin_code'];
        } elseif (isset($options['class'])) {
            $class = $options['class'];
        } elseif (($data = $form->getData()) instanceof PersistentCollection) {
            $class = $data->getTypeClass()->getName();
        }

        if (isset($code)) {
            $childAdmin = $this->adminPool->getAdminByAdminCode($code);
        } elseif (isset($class)) {
            $childAdmin = $this->adminPool->getAdminByClass($class);
        } else {
            throw new \InvalidArgumentException('Configure "class" or "admin_code" options for obtains the admin instance.');
        }

        if (!$childAdmin) {
            $message = isset($class)
                ? sprintf('Create a Admin class for "%s"', $class)
                : sprintf('Admin code "%s" not found.', $options['admin_code']);

            throw new \LogicException($message);
        }

        $parentClass = $form->getParent()->getConfig()->getOption('data_class');

        if (isset($options['parent_admin_code'])) {
            $parentAdmin = $this->adminPool->getAdminByAdminCode($options['parent_admin_code']);
        } else {
            $parentAdmin = $this->adminPool->getAdminByClass($parentClass);
        }

        if (!$parentAdmin->hasChild($childAdmin->getCode())) {
            throw new \LogicException(
                sprintf('Add the admin "%s" as child of "%s"', get_class($childAdmin), get_class($parentAdmin))
            );
        }

        $parentAdmin->setSubject($form->getParent()->getData());
        $childAdmin = $parentAdmin->getChild($childAdmin->getCode());

        /** @var AbstractAdmin $childAdmin */
        if (!$childAdmin->isEmbedded()) {
            throw new \LogicException('The child Admin should be marked as embedded to embed into a parent.');
        }

        $id = $parentAdmin->getUrlsafeIdentifier($parentAdmin->getSubject());

        if (null === $id) {
            throw new \LogicException(
                sprintf(
                    'You cannot create embedded admin from new parent instances. Check the form configuration for "%s" admin class.',
                    get_class($parentAdmin))
            );
        }

        $view->vars['parentId'] = $id;
        $view->vars['admin'] = $childAdmin;
        $view->vars['label'] = $options['title'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $titleNormalizer = function (Options $options, $title) {
            if (true === $title) {
                return $options['label'];
            }

            return $title;
        };

        $resolver->setDefaults([
            'class' => null,
            // needed for multiple admin attached to same entity
            'admin_code' => null,
            'parent_admin_code' => null,
            'title' => false,
            'required' => false,
        ]);

        $resolver->setNormalizer('title', $titleNormalizer);
    }
}
