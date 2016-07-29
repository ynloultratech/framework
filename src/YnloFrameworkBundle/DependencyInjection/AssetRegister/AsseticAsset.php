<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister;

class AsseticAsset
{
    const JAVASCRIPT = 'js';
    const STYLESHEET = 'css';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * AsseticAsset constructor.
     *
     * @param string       $name
     * @param array|string $inputs
     * @param array        $filters
     */
    public function __construct($name, $inputs, array $filters = [])
    {
        $this->name = $name;
        $this->inputs = is_string($inputs) ? [$inputs] : $inputs;
        $this->filters = $filters;

        //detect the type based on content
        if (preg_match('/\.css$/', current($this->inputs))) {
            $this->type = self::STYLESHEET;
        } else {
            $this->type = self::JAVASCRIPT;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * isJavascript
     *
     * @return bool
     */
    public function isJavascript()
    {
        return $this->type === self::JAVASCRIPT;
    }

    /**
     * isJavascript
     *
     * @return bool
     */
    public function isStylesheet()
    {
        return $this->type === self::STYLESHEET;
    }
}