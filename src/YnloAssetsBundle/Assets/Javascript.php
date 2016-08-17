<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Assets;

/**
 * Javascript.
 */
class Javascript extends AbstractAsseticAsset
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::JAVASCRIPT;
    }
}
