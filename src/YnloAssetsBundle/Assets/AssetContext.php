<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

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
     * @var array|AbstractAsset[]
     */
    protected $assets = [];

    /**
     * AssetContext constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
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
     * @return array|AbstractAsset[]
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
     * Resolve assets names variants for array fo assets
     *
     * @param array $assetsArray
     *
     * @return array resolved variant names
     */
    private function resolveVariants(array $assetsArray)
    {
        foreach ($assetsArray as $assetName) {
            $hasVariants = false;
            if (AssetRegistry::hasNamedAsset($assetName.'_js')) {
                $assetsArray[] = $assetName.'_js';
                $hasVariants = true;
            }
            if (AssetRegistry::hasNamedAsset($assetName.'_css')) {
                $assetsArray[] = $assetName.'_css';
                $hasVariants = true;
            }
            if ($hasVariants && !AssetRegistry::hasNamedAsset($assetName)) {
                $assetsArray = array_flip($assetsArray);
                unset($assetsArray[$assetName]);
                $assetsArray = array_flip($assetsArray);
            }
        }

        return $assetsArray;
    }

    /**
     * Resolve name of assets to include
     * in this context and populate the list of assets
     */
    private function resolveInclusions()
    {
        $includes = $this->getInclude();
        $includes = $this->resolveVariants($includes);

        //resolve inclusions
        foreach ($includes as $include) {
            if (AssetRegistry::hasNamedAsset($include)) {
                $included = true;
                $asset = AssetRegistry::getAsset($include);
                if ($asset instanceof AssetBundle) {
                    foreach ($asset->getAssets() as $bundleAsset) {
                        $this->assets[$bundleAsset->getName()] = clone $bundleAsset;
                    }
                } else {
                    $this->assets[$asset->getName()] = clone $asset;
                }
            } else {
                throw new \InvalidArgumentException(sprintf('There are not asset called %s', $include));
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
        $assetsToExclude = [];
        $excludes = $this->getExclude();
        $excludes = $this->resolveVariants($excludes);

        foreach ($excludes as $exclude) {
            if (AssetRegistry::hasNamedAsset($exclude)) {
                $asset = AssetRegistry::getAsset($exclude);
                if ($asset instanceof AssetBundle) {
                    $namedAssets = $asset->getAssetsNames();
                } else {
                    $namedAssets = [$asset->getName()];
                }

                $assetsToExclude = array_merge($assetsToExclude, $namedAssets);
            } else {
                throw new \InvalidArgumentException(sprintf('There are not asset called %s', $exclude));
            }
        }

        foreach ($assetsToExclude as $excludeName) {
            unset($this->assets[$excludeName]);
        }
    }

    /**
     * Override some asset with given version
     */
    private function overrideAssets()
    {
        if ($this->getOverride()) {
            foreach ($this->getOverride() as $name => $newAssetPath) {
                if (isset($this->assets[$name])) {
                    $this->assets[$name]->setPath($newAssetPath);
                } else {
                    $msg = sprintf('Invalid configuration for asset context(%s) : There are not asset called `%s` to override', $this->getName(), $name);
                    throw new \RuntimeException($msg);
                }
            }
        }
    }
}