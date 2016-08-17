<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework;

use Symfony\Component\HttpKernel\Kernel;
use YnloFramework\Component\KernelBuilder\KernelBuilder;

/**
 * AppBuilder.
 */
class AppBuilder
{
    /**
     * Prepare the kernel to setup.
     *
     * @param Kernel $kernel
     *
     * @return KernelBuilder
     */
    public static function setUp(Kernel $kernel)
    {
        return new KernelBuilder($kernel);
    }
}
