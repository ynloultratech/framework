<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

use Opis\Closure\SerializableClosure;

class AutocompleteContext implements AutocompleteContextInterface
{
    /**
     * @var string
     */
    private $provider;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $serializedParams;

    /**
     * AutocompleteContext constructor.
     *
     * @param string $provider name of the auto-complete provider to use
     * @param array  $parameters
     */
    public function __construct($provider = null, array $parameters = [])
    {
        $this->provider = $provider;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameter($name, $default = null)
    {
        if ($this->hasParameter($name)) {
            return $this->parameters[$name];
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        $this->serializedParams = $this->parameters;
        foreach ($this->serializedParams as $name => &$value) {
            if (is_callable($value) && !($value instanceof SerializableClosure)) {
                $value = new SerializableClosure($value);
            }
        }

        return ['provider', 'serializedParams'];
    }

    /**
     * @inheritDoc
     */
    public function __wakeup()
    {
        foreach ($this->serializedParams as $name => $value) {
            if ($value instanceof SerializableClosure) {
                $value = $value->getClosure();
            }
            $this->setParameter($name, $value);
        }
    }
}