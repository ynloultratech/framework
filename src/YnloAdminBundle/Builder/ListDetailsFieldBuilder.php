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
 * Class ListDetailsFieldBuilder.
 */
class ListDetailsFieldBuilder implements ListFieldBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportThisField(FieldDescriptionInterface $fieldDescription)
    {
        return '_details' === $fieldDescription->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        if (null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list__details.html.twig');
        }

        if (null === $fieldDescription->getType()) {
            $fieldDescription->setType('_details');
        }

        if (null === $fieldDescription->getOption('name')) {
            $fieldDescription->setOption('name', 'Details');
        }

        if (null === $fieldDescription->getOption('header_style')) {
            $fieldDescription->setOption('header_style', 'width:20px');
        }

        if (null === $fieldDescription->getOption('details_template')) {
            throw new \LogicException('The option "details_template" is required');
        }

        //encode the template
        $template = base64_encode($fieldDescription->getOption('details_template'));
        $fieldDescription->setOption('details_template_encoded', $template);

        if (null === $fieldDescription->getOption('ajax')) {
            $fieldDescription->setOption('ajax', false);
        }

        if (null === $fieldDescription->getOption('code')) {
            $fieldDescription->setOption('code', '_details');
        }

        //hide default label
        if ($fieldDescription->getOption('label') == '_details' || $fieldDescription->getOption('label') == 'Details') {
            $fieldDescription->setOption('label', ' ');
        }
    }
}
