<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Exception;

class MissingBundleException extends \Exception
{
    /**
     * MissingBundleException constructor.
     *
     * @param string $bundle
     */
    public function __construct($bundle)
    {
        $message = sprintf('The bundle "%s" can`t be added to the kernel. Class not found.', $bundle);
        parent::__construct($message);
    }
}