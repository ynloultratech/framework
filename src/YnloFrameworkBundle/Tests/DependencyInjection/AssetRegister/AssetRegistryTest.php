<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Tests\DependencyInjection\AssetRegister;

use Sonata\BlockBundle\DependencyInjection\SonataBlockExtension;
use Symfony\Bundle\AsseticBundle\DependencyInjection\AsseticExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use YnloFramework\YnloAdminBundle\DependencyInjection\YnloAdminExtension;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegistry;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\YnloFrameworkExtension;

class AssetRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $extensions = [
            //side effects requirements
            'twig' => new TwigExtension(),
            'sonata_block' => new SonataBlockExtension(),
            //assetic
            'assetic' => new AsseticExtension(),
            //internal
            'ynlo_framework' => new YnloFrameworkExtension(),
            'ynlo_admin' => new YnloAdminExtension(),
        ];

        $this->container = new ContainerBuilder();
        foreach ($extensions as $name => $class) {
            $this->container->registerExtension($class);
            $this->container->prependExtensionConfig($name, []);
        }
    }

    public function testGetRegisteredAssets()
    {
        $registry = new AssetRegistry($this->container);
        $assets = $registry->getAsseticAssetsArray();
        self::assertContains('bundles/ynloframework/vendor/jquery/jquery.min.js', $assets['all_js']['inputs']);
        self::assertContains('bundles/ynloframework/js/core.yfp.js', $assets['all_js']['inputs']);
        self::assertContains('bundles/ynloadmin/js/admin.yfp.js', $assets['all_js']['inputs']);
        self::assertContains('bundles/ynloframework/vendor/bootstrap/css/bootstrap.min.css', $assets['all_css']['inputs']);
        self::assertContains('bundles/ynloadmin/vendor/admin-lte/css/AdminLTE.min.css', $assets['all_css']['inputs']);
        self::assertContains('bundles/ynloframework/vendor/jquery/jquery.min.js', $assets['jquery']['inputs']);
    }
}
