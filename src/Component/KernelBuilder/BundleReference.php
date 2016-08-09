<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\Component\KernelBuilder;

/**
 * BundleReference
 */
class BundleReference implements BundleReferenceInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $package;

    /**
     * BundleReference constructor.
     *
     * @param string $name
     * @param string $class
     * @param string $package
     */
    public function __construct($name, $class, $package = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     *
     * @return $this
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }
}
