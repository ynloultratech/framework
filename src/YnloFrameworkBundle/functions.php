<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
if (!function_exists('array_key_value')) {
    /**
     * Extract some value from array using a key path,
     * return default value if the path don't exist.
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
