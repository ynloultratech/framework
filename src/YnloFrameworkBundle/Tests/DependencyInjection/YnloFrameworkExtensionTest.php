<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Tests\DependencyInjection;

use YnloFramework\YnloFrameworkBundle\DependencyInjection\YnloFrameworkExtension;

class YnloFrameworkExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterAssets()
    {
        $extension = new YnloFrameworkExtension();

        $assets = [
            'ynlo_framework_js' => 'framework.js',
            'pace_js' => 'pace.js',
            'jquery_form' => 'jquery.form.js',
            'ynlo_debugger' => 'debugger.js',
        ];
        $config = [
            'pace' => true,
            'ajax_forms' => true,
            'debug' => true,
            'icons' => ['fontawesome'],
        ];
        $filteredAssets = $extension->filterAssets($assets, $config);

        self::assertEquals($assets, $filteredAssets);

        //disable pace
        $configModified = $config;
        $configModified['pace'] = false;
        $assetsModified = $assets;
        unset($assetsModified['pace_js']);
        $filteredAssets = $extension->filterAssets($assets, $configModified);
        self::assertEquals($assetsModified, $filteredAssets);

        //disable ajax forms
        $configModified = $config;
        $configModified['ajax_forms'] = false;
        $assetsModified = $assets;
        unset($assetsModified['jquery_form']);
        $filteredAssets = $extension->filterAssets($assets, $configModified);
        self::assertEquals($assetsModified, $filteredAssets);

        //disable debug
        $configModified = $config;
        $configModified['debug'] = false;
        $assetsModified = $assets;
        unset($assetsModified['ynlo_debugger']);
        $filteredAssets = $extension->filterAssets($assets, $configModified);
        self::assertEquals($assetsModified, $filteredAssets);
    }
}
