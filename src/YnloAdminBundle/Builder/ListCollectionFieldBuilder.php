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

/**
 * Class ListCollectionFieldBuilder
 */
class ListCollectionFieldBuilder implements ListFieldBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportThisField(FieldDescriptionInterface $fieldDescription)
    {
        return $fieldDescription->getName() === 'collection';
    }

    /**
     * {@inheritDoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list_collection.html.twig');
    }
}
