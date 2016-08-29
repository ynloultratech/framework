<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * AbstractAsseticAsset.
 */
abstract class AbstractAsseticAsset extends AbstractAsset
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $assetPath, $filters = [])
    {
        parent::__construct($name, $assetPath);

        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->filters, parent::serialize()]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->filters, $parentSerialized) = unserialize($serialized);
        parent::unserialize($parentSerialized);
    }
}
