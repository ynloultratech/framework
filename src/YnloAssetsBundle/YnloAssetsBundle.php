<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegistry;

class YnloAssetsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        //re-build the registry using a saved parameter
        AssetRegistry::unserialize($this->container->getParameter('ynlo.assets'));
    }
}
