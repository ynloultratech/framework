<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\Component\KernelBuilder;

/**
 * Class BundleReferenceInterface
 */
interface BundleReferenceInterface
{
    /**
     * Bundle class
     *
     * @return string
     */
    public function getClass();

    /**
     * Bundle name
     *
     * @return string
     */
    public function getName();

    /**
     * Composer package for information purposes
     *
     * @return string
     */
    public function getPackage();
}
