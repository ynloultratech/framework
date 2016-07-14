<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

class AutocompleteProviderManager
{
    /**
     * @var array
     */
    protected $providers;

    /**
     * @param AutocompleteProviderInterface $provider
     */
    public function add(AutocompleteProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param string $name
     *
     * @return AutocompleteProviderInterface
     */
    public function getProvider($name)
    {
        if (!isset($this->providers[$name])) {
            throw new \RuntimeException(sprintf('Invalid auto-complete provider `%s`', $name));
        }

        return $this->providers[$name];
    }

    /**
     * @return array|AutocompleteProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }
}