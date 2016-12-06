<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\KernelBuilder;

interface BundleReferenceInterface
{
    /**
     * Bundle class.
     *
     * @return string
     */
    public function getClass();

    /**
     * Bundle name.
     *
     * @return string
     */
    public function getName();

    /**
     * Composer package for information purposes.
     *
     * @return string
     */
    public function getPackage();
}
