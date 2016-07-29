<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

interface AutocompleteContextInterface
{
    /**
     * Name of the service to use to fetch results.
     *
     * @return string
     */
    public function getProvider();

    /**
     * @param string $provider
     *
     * @return $this
     */
    public function setProvider($provider);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters($parameters);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParameter($name, $value);

    /**
     * @param string     $name
     * @param null|mixed $default
     *
     * @return mixed
     */
    public function getParameter($name, $default = null);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function hasParameter($name);
}
