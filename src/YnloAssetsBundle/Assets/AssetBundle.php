<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * Class AssetBundle.
 */
class AssetBundle
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|AssetInterface[]
     */
    protected $assets = [];

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * AssetBundle constructor.
     *
     * @param string                       $name
     * @param array|AbstractAsseticAsset[] $assets
     */
    public function __construct($name, $assets)
    {
        $this->name = $name;
        foreach ($assets as $asset) {
            if (!$asset instanceof AbstractAsseticAsset) {
                throw new \LogicException('Only assetic assets can be compiled into a bundle');
            }
            $this->assets[$asset->getName()] = clone $asset;
            $this->filters = array_unique(array_merge($this->filters, $asset->getFilters()));
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
     * @return array|AssetInterface[]
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * getPaths.
     *
     * @return array
     */
    public function getPaths()
    {
        $paths = [];
        foreach ($this->getAssets() as $input) {
            $paths[] = $input->getPath();
        }

        return $paths;
    }

    /**
     * @return array
     */
    public function getAssetsNames()
    {
        $names = [];
        foreach ($this->getAssets() as $input) {
            $names[] = $input->getName();
        }

        return $names;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
