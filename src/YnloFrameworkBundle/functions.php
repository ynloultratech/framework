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
if (!function_exists('array_value_expected')) {
    /**
     * Extract some value from array using a key path,
     * return default value if the path don't exist
     *
     * Helpful to check a expected value of array in conditions
     *
     * e.g.
     * if (array_key_value($array,'options.enabled') === true){
     *   //...
     * }
     *
     * @param      $array
     * @param      $path
     * @param null $default
     *
     * @return mixed|null
     */
    function array_key_value($array, $path, $default = null)
    {
        $pathArray = explode('.', $path);
        foreach ($pathArray as $index) {
            if (array_key_exists($index, $array)) {
                $value = $array[$index];
                if (is_array($value)) {
                    $array = $value;
                }
            } else {
                return $default;
            }
        }
        if (isset($value)) {
            return $value;
        }

        return $default;
    }
}
