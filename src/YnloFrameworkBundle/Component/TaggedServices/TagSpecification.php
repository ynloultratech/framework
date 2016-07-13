<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\Component\TaggedServices;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class TagSpecification
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TagSpecification constructor.
     *
     * @param string             $id
     * @param string             $name
     * @param array              $attributes
     * @param ContainerInterface $container
     */
    public function __construct($id, $name, array $attributes, ContainerInterface $container = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function getService()
    {
        return $this->container->get($this->getId());
    }
}