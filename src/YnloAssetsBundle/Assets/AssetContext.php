<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * Class AssetContext.
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
     * getAssets.
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
     * Resolve assets names variants for array of assets.
     *
     * @param array $assetsArray
     *
     * @return array resolved variant names
     */
    private function resolveVariants(array $assetsArray)
    {
        $assetsArray = array_values($assetsArray);//force indexed keys
        foreach ($assetsArray as $offset => $assetName) {
            $hasVariants = false;
            if (AssetRegistry::hasNamedAsset($assetName.'_js')) {
                array_splice($assetsArray, $offset, 0, $assetName.'_js');
                $hasVariants = true;
            }
            if (AssetRegistry::hasNamedAsset($assetName.'_css')) {
                array_splice($assetsArray, $offset, 0, $assetName.'_css');
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
     * in this context and populate the list of assets.
     */
    private function resolveInclusions()
    {
        $includes = $this->getInclude();
        $includes = $this->resolveVariants($includes);

        //resolve inclusions
        foreach ($includes as $include) {
            if (AssetRegistry::hasNamedAsset($include)) {
                $asset = AssetRegistry::getAsset($include);
                if ($asset instanceof AssetBundle) {
                    foreach ($asset->getAssets() as $bundleAsset) {
                        $this->assets[$bundleAsset->getName()] = clone $bundleAsset;
                    }
                } else {
                    $this->assets[$asset->getName()] = clone $asset;
                }
            } else {
                $name = 'asset'.mt_rand(1111, 99999999);
                $this->assets[$name] = AssetFactory::asset($name, $include);
            }
        }
    }

    /**
     * Resolve name of assets to exclude
     * from this context and clean the list of assets.
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
            //don`t exclude assets explicitly included
            //e.g. include: fontawesome, exclude: all
            if (!in_array($excludeName, $this->getInclude())) {
                unset($this->assets[$excludeName]);
            }
        }
    }

    /**
     * Override some asset with given version.
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
