<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * JavascriptModule.
 */
class JavascriptModule extends AbstractAsset
{
    /**
     * @var string
     */
    protected $cdn;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var array
     */
    protected $dependencies = [];

    /**
     * @var array
     */
    protected $exports = [];

    /**
     * @var string
     */
    protected $init;

    /**
     * Array of jquery plugins functions used by this asset
     * this is used to create an autoloader for some jquery plugins.
     *
     * @var array
     */
    protected $jqueryPlugins = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $assetPath)
    {
        parent::__construct($name, $assetPath);

        if ($this->moduleName === null) {
            $this->moduleName = preg_replace('/_js$/', '', $name);
        }
    }

    /**
     * @return string
     */
    public function getCdn()
    {
        return $this->cdn;
    }

    /**
     * @param string $cdn
     *
     * @return $this
     */
    public function setCdn($cdn)
    {
        $this->cdn = $cdn;

        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     *
     * @return $this
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param array $dependencies
     *
     * @return $this
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * @return array
     */
    public function getExports()
    {
        return $this->exports;
    }

    /**
     * @param array $exports
     *
     * @return $this
     */
    public function setExports($exports)
    {
        $this->exports = $exports;

        return $this;
    }

    /**
     * @return string
     */
    public function getInit()
    {
        return $this->init;
    }

    /**
     * @param string $init
     *
     * @return $this
     */
    public function setInit($init)
    {
        $this->init = $init;

        return $this;
    }

    /**
     * addJqueryPlugin.
     *
     * @param string $functionName
     *
     * @return $this
     */
    public function addJqueryPlugin($functionName)
    {
        $this->jqueryPlugins[] = $functionName;

        return $this;
    }

    /**
     * @return array
     */
    public function getJqueryPlugins()
    {
        return $this->jqueryPlugins;
    }

    /**
     * @param array $jqueryPlugins
     *
     * @return $this
     */
    public function setJqueryPlugins($jqueryPlugins)
    {
        $this->jqueryPlugins = $jqueryPlugins;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::JAVASCRIPT;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            [
                $this->cdn,
                $this->moduleName,
                $this->dependencies,
                $this->exports,
                $this->init,
                $this->jqueryPlugins,
                parent::serialize(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->cdn,
            $this->moduleName,
            $this->dependencies,
            $this->exports,
            $this->init,
            $this->jqueryPlugins,
            $parentSerialized)
            = unserialize($serialized);

        parent::unserialize($parentSerialized);
    }
}
