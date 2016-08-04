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
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Guesser\TypeGuesserInterface;
use Sonata\DoctrineORMAdminBundle\Builder\ListBuilder as BaseListBuilder;
use YnloFramework\YnloFrameworkBundle\Component\TaggedServices\TaggedServices;

/**
 * ListBuilder
 */
class ListBuilder extends BaseListBuilder
{
    /**
     * @var TaggedServices
     */
    protected $taggedServices;

    /**
     * {@inheritdoc}
     */
    public function __construct(TypeGuesserInterface $guesser, array $templates = [])
    {
        parent::__construct($guesser, isset($templates['types']['list']) ? $templates['types']['list'] : $templates);
    }

    /**
     * {@inheritdoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        $specifications = $this->taggedServices->findTaggedServices('ynlo.admin.list_field_builder');
        foreach ($specifications as $specification) {
            if (($service = $specification->getService()) && $service instanceof ListFieldBuilderInterface) {
                if ($service->supportThisField($fieldDescription)) {
                    $service->fixFieldDescription($admin, $fieldDescription);
                }
            } else {
                $msg = sprintf('The service `%s` tagged as `%s` should implements, %s', $specification->getId(), $specification->getName(), ListBuilderInterface::class);
                throw new \RuntimeException($msg);
            }
        }

        parent::fixFieldDescription($admin, $fieldDescription);
    }

    /**
     * @param FieldDescriptionInterface $fieldDescription
     *
     * @return FieldDescriptionInterface
     */
    public function buildActionFieldDescription(FieldDescriptionInterface $fieldDescription)
    {
        //overwritten, now in a separated class ListActionFieldBuilder
    }

    /**
     * @param TaggedServices $taggedServices
     *
     * @return $this
     */
    public function setTaggedServices($taggedServices)
    {
        $this->taggedServices = $taggedServices;

        return $this;
    }
}
