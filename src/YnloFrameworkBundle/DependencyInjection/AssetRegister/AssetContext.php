<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister;

/**
 * Class AssetContext
 */
class AssetContext
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var AssetRegistry
     */
    protected $assetRegistry;

    /**
     * @var array
     */
    protected $include = [];

    /**
     * @var array
     */
    protected $exclude = [];

    /**
     * @var array
     */
    protected $override = [];

    /**
     * @var array|AsseticAsset[]
     */
    protected $assets = [];

    /**
     * AssetContext constructor.
     *
     * @param string        $name
     * @param AssetRegistry $assetRegistry
     */
    public function __construct($name, AssetRegistry $assetRegistry)
    {
        $this->name = $name;
        $this->assetRegistry = $assetRegistry;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return AssetRegistry
     */
    public function getAssetRegistry()
    {
        return $this->assetRegistry;
    }

    /**
     * @param AssetRegistry $assetRegistry
     *
     * @return $this
     */
    public function setAssetRegistry($assetRegistry)
    {
        $this->assetRegistry = $assetRegistry;

        return $this;
    }

    /**
     * @return array
     */
    public function getInclude()
    {
        return $this->include;
    }

    /**
     * @param array $include
     *
     * @return $this
     */
    public function setInclude(array $include)
    {
        $this->include = $include;

        return $this;
    }

    /**
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param array $exclude
     *
     * @return $this
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;

        return $this;
    }

    /**
     * @return array
     */
    public function getOverride()
    {
        return $this->override;
    }

    /**
     * @param array $override
     *
     * @return $this
     */
    public function setOverride(array $override)
    {
        $this->override = $override;

        return $this;
    }

    /**
     * getAssets
     *
     * @return array|AsseticAsset[]
     */
    public function getAssets()
    {
        $this->assets = [];
        $this->resolveInclusions();
        $this->resolveExclusions();
        $this->overrideAssets();

        return $this->assets;
    }

    /**
     * Resolve name of assets to include
     * in this context and populate the list of assets
     */
    private function resolveInclusions()
    {
        //resolve inclusions
        foreach ($this->getInclude() as $include) {
            $namedAssets = [
                $include,
                $include.'_js',
                $include.'_css',
            ];
            $isNamedAsset = false;
            foreach ($namedAssets as $namedAsset) {
                if ($this->getAssetRegistry()->hasNamedAsset($namedAsset)) {
                    $this->assets[] = $this->getAssetRegistry()->getNamedAsset($namedAsset);
                    $isNamedAsset = true;
                }
            }

            if (!$isNamedAsset) {
                $this->assets[] = new AsseticAsset('asset_'.mt_rand(111, 99999), $include);
            }
        }
    }

    /**
     * Resolve name of assets to exclude
     * from this context and clean the list of assets
     */
    private function resolveExclusions()
    {
        //resolve exclusions
        if ($this->getExclude()) {
            $assetsToExclude = [];
            foreach ($this->getExclude() as $exclude) {
                $namedAssets = [
                    $exclude,
                    $exclude.'_js',
                    $exclude.'_css',
                ];

                foreach ($namedAssets as $namedAsset) {
                    if ($this->getAssetRegistry()->hasNamedAsset($namedAsset)) {
                        $assetsToExclude = array_merge($assetsToExclude, $this->getAssetRegistry()->getNamedAsset($namedAsset)->getInputs());
                    } else {
                        $assetsToExclude[] = $exclude;
                    }
                }
            }

            /** @var AsseticAsset $asset */
            foreach ($this->assets as $name => $asset) {
                $originalAssets = $asset->getInputs();
                foreach ($assetsToExclude as $excludeAssetPath) {
                    foreach ($originalAssets as $index => $origin) {
                        if ($origin === $excludeAssetPath) {
                            unset($originalAssets[$index]);
                        }
                    }
                }
                $this->assets[$name] = new AsseticAsset($name, $originalAssets, $asset->getFilters());
            }
        }
    }

    /**
     * Override some asset with given version
     */
    private function overrideAssets()
    {
        if ($this->getOverride()) {
            foreach ($this->getOverride() as $name => $newAssetPath) {
                if ($this->getAssetRegistry()->hasNamedAsset($name)) {
                    //original assets inside this named asset
                    $originalAssets = $this->getAssetRegistry()->getNamedAsset($name)->getInputs();

                    $placeholder = 0;
                    foreach ($this->assets as $assetName => $asset) {
                        $includedAssets = $asset->getInputs();
                        foreach ($includedAssets as $index => $includedPath) {
                            foreach ($originalAssets as $originPath) {
                                if ($includedPath === $originPath) {
                                    //try to keep replacements in the same place of original paths
                                    if (is_array($newAssetPath)) {
                                        if (isset($newAssetPath[$placeholder])) {
                                            $includedAssets[$index] = $newAssetPath[$placeholder];
                                        } else {
                                            //if array of new assets is less than original
                                            //remove the rest
                                            unset($includedAssets[$index]);
                                        }
                                    } else {
                                        $includedAssets[$index] = $newAssetPath;
                                    }

                                    $placeholder++;
                                }
                            }
                        }
                        $this->assets[$assetName] = new AsseticAsset($assetName, $includedAssets, $asset->getFilters());
                    }

                } else {
                    $msg = sprintf('Invalid configuration for asset context(%s) : There are not asset called `%s` to override', $this->getName(), $name);
                    throw new \RuntimeException($msg);
                }
            }
        }
    }
}