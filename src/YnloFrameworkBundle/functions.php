<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

if (!function_exists('dump')) {
    /** @param mixed $data */
    function dump($data)
    {
        foreach (func_get_args() as $var) {
            \Symfony\Component\VarDumper\VarDumper::dump($var);
        }
    }
}