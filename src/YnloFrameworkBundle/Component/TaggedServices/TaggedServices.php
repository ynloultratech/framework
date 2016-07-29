<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
 */

namespace YnloFramework\YnloFrameworkBundle\Component\TaggedServices;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TaggedServices
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $servicesByTags = [];

    /**
     * TaggedServices constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     * @param string $tagName
     * @param array  $tagAttributes
     */
    public function addSpecification($id, $tagName, array $tagAttributes = [])
    {
        $this->servicesByTags[$tagName][] = new TagSpecification($id, $tagName, $tagAttributes, $this->container);
    }

    /**
     * findTaggedServices
     *
     * @param string $tag
     *
     * @return array|TagSpecification[]
     */
    public function findTaggedServices($tag)
    {
        if (array_key_exists($tag, $this->servicesByTags)) {
            return $this->servicesByTags[$tag];
        }

        return [];
    }
}