<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Builder;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

/**
 * Class ListEnumFieldBuilder
 */
class ListEnumFieldBuilder implements ListFieldBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportThisField(FieldDescriptionInterface $fieldDescription)
    {
        return 'enum' === $fieldDescription->getType() && null === $fieldDescription->getTemplate();
    }

    /**
     * {@inheritDoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list_enum.html.twig');

        /** @var ModelManager $modelManager */
        $modelManager = $admin->getModelManager();
        if (null === $fieldDescription->getOption('enum_type') && $modelManager->hasMetadata($admin->getClass())) {
            $mapping = $modelManager->getMetadata($admin->getClass())->getFieldMapping($fieldDescription->getName());
            $fieldDescription->setOption('enum_type', $mapping['type']);
        }
    }
}