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
use YnloFramework\YnloAssetsBundle\Assets\AssetFactory;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegisterInterface;

class YnloAdminExtension extends Extension implements AssetRegisterInterface, PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'ynlo.js_plugin.admin',
            [
                'icheck' => $config['icheck'],
            ]
        );

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

        //set twig globals
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig('ynlo_admin'));
        $vendorConfig = $container->getExtensionConfig('twig')[0];
        $vendorConfig['globals']['admin_body_classes'][] = 'skin-'.$config['skin'];

        if ($config['mode'] === 'fixed') {
            $vendorConfig['globals']['admin_body_classes'][] = 'fixed';
        } else {
            $vendorConfig['globals']['admin_body_classes'][] = 'layout-boxed';
        }

        if ($config['sidebar_mini']) {
            $vendorConfig['globals']['admin_body_classes'][] = 'sidebar-mini';
        }

        if ($config['collapsed_sidebar']) {
            $vendorConfig['globals']['admin_body_classes'][] = 'sidebar-collapse';
        }

        $container->prependExtensionConfig('twig', $vendorConfig);

        //include required bundles into assetic
        $asseticConfig = $container->getExtensionConfig('assetic')[0];
        $asseticConfig['bundles'][] = 'YnloAdminBundle';
        $asseticConfig['bundles'][] = 'SonataAdminBundle';
        $container->prependExtensionConfig('assetic', $asseticConfig);

        //admin asset context
        $ynloAssetsConfig = $container->getExtensionConfig('ynlo_assets')[0];
        $ynloAssetsConfig['contexts']['admin'] = [
            'include' => ['all'],
            'override' => [
                'pace_css' => 'bundles/ynloadmin/vendor/admin-lte/plugins/pace/pace.min.css',
            ],
        ];
        $container->prependExtensionConfig('ynlo_assets', $ynloAssetsConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function registerAssets(array $config, ContainerBuilder $containerBuilder)
    {
        $assets = [];
        //Vendor
        $assets[] = AssetFactory::asset('admin_lte_css', 'bundles/ynloadmin/vendor/admin-lte/css/AdminLTE.min.css');
        $assets[] = AssetFactory::asset('admin_lte_skins_css', 'bundles/ynloadmin/vendor/admin-lte/css/skins/_all-skins.min.css');
        $assets[] = AssetFactory::module('admin_lte_js', 'bundles/ynloadmin/vendor/admin-lte/js/app.min.js')->setDependencies(['bootstrap']);
        $assets[] = AssetFactory::module('jquery_slim_scroll_js', 'bundles/ynloadmin/vendor/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js')
            ->setJqueryPlugins(['slimScroll', 'slimscroll']);

        $assets[] = AssetFactory::asset('jquery_confirm_exit_js', 'bundles/sonataadmin/jquery/jquery.confirmExit.js');

        if ($config['icheck']) {
            $assets[] = AssetFactory::module('icheck_js', 'bundles/ynloadmin/vendor/admin-lte/plugins/iCheck/icheck.min.js');

            $theme = 'flat-blue';
            if (is_string($config['icheck']) && $config['icheck'] !== $theme) {
                $theme = $config['icheck'];
            }

            if (strpos($theme, '-') !== false) {
                $theme = str_replace('-', '/', $theme);
            } else {
                $theme = $theme.'/'.$theme;
            }

            $assets[] = AssetFactory::asset('icheck_theme_css', "bundles/ynloadmin/vendor/admin-lte/plugins/iCheck/$theme.css");
        }

        $assets[] = AssetFactory::module('bootstrap_editable_js', 'bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js')
            ->setDependencies(['bootstrap'])
            ->setJqueryPlugins(['editable']);

        $assets[] = AssetFactory::asset('bootstrap_editable_css', 'bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css');
        $assets[] = AssetFactory::module('jquery_ui_js', 'bundles/sonataadmin/vendor/jqueryui/ui/minified/jquery-ui.min.js')
            ->setJqueryPlugins(['sortable']);

        $assets[] = AssetFactory::asset('jquery_ui_css', 'bundles/sonataadmin/vendor/jqueryui/themes/base/jquery-ui.css');
        $assets[] = AssetFactory::asset('tree_view_js', 'bundles/sonataadmin/treeview.js');
        $assets[] = AssetFactory::asset('jquery_waypoints_js', 'bundles/sonataadmin/vendor/waypoints/lib/jquery.waypoints.min.js');
        $assets[] = AssetFactory::asset('jquery_waypoints_sticky_js', 'bundles/sonataadmin/vendor/waypoints/lib/shortcuts/sticky.min.js');
        $assets[] = AssetFactory::module('sonata_admin_js', 'bundles/sonataadmin/Admin.js');
        //Internal
        $assets[] = AssetFactory::asset('ynlo_admin_js', 'bundles/ynloadmin/js/admin.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_admin_css', 'bundles/ynloadmin/css/ynlo-admin.css', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('sonata_admin_override_js', 'bundles/ynloadmin/js/sonata_admin_override.js');
        $assets[] = AssetFactory::asset('ynlo_admin_list_batch_js', 'bundles/ynloadmin/js/admin_list_batch.yfp.js', ['yfp_config_dumper']);
        $assets[] = AssetFactory::asset('ynlo_admin_list_details_js', 'bundles/ynloadmin/js/admin_list_details.yfp.js', ['yfp_config_dumper']);

        return $assets;
    }
}
