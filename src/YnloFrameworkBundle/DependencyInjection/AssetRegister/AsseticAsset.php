<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
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