<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\KernelBuilder\Exception;

class MissingPackageException extends \Exception
{
    /**
     * MissingBundleException constructor.
     *
     * @param string|array $package package or array of missing packages
     */
    public function __construct($package)
    {
        $packageList = '';
        foreach ((array) $package as $pack) {
            $packageList .= $pack."\n";
        }
        $msg = sprintf("The following composer packages are required to compile the Kernel,\n\n %s\n Install these packages and try again.", $packageList);
        parent::__construct($msg);
    }
}
