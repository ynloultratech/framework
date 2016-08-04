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
 * This interface is used to create some type of fields in the
 * list without the need of override the entire ListBuilder
 */
interface ListFieldBuilderInterface
{

    /**
     * @param FieldDescriptionInterface $fieldDescription
     *
     * @return bool
     */
    public function supportThisField(FieldDescriptionInterface $fieldDescription);

    /**
     * @param AdminInterface            $admin
     * @param FieldDescriptionInterface $fieldDescription
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription);
}
