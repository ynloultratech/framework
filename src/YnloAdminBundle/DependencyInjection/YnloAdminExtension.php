<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AsseticAsset;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetRegisterInterface;

class YnloAdminExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);


        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        //auto-configure block bundle
        $vendorConfig = $container->getExtensionConfig('sonata_block')[0];
        $vendorConfig['default_contexts'][] = 'admin';
        $vendorConfig['blocks']['sonata.admin.block.admin_list']['contexts'][] = 'admin';
        $container->prependExtensionConfig('sonata_block', $vendorConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function registerInternalAssets()
    {
        return [
            new AsseticAsset('ynlo_admin_js', 'bundles/ynloadmin/js/admin.yfp.js', ['yfp_config_dumper']),

            //extra
            new AsseticAsset('admin_lte_css', 'bundles/sonataadmin/vendor/admin-lte/dist/css/AdminLTE.min.css'),
            new AsseticAsset('admin_lte_black_skin_css', 'bundles/sonataadmin/vendor/admin-lte/dist/css/skins/skin-black.min.css'),
            new AsseticAsset(
                'sonata_admin_style', [
                    'bundles/sonataadmin/css/styles.css',
                    'bundles/sonataadmin/css/layout.css',
                    'bundles/sonataadmin/css/tree.css',
                ]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filterAssets(array $assets, array $config)
    {
        return $assets;
    }
}
