<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetConfiguration;

class Configuration implements ConfigurationInterface
{
    use AssetConfiguration;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ynlo_admin')->addDefaultsIfNotSet()->children();

        $rootNode->enumNode('skin')->values(
            [
                'blue',
                'black',
                'purple',
                'green',
                'red',
                'yellow',
                'blue-light',
                'black-light',
                'purple-light',
                'green-light',
                'red-light',
                'yellow-light',
            ]
        )->defaultValue('blue');

        $rootNode->enumNode('mode')->values(['fixed', 'boxed'])->defaultValue('fixed');
        $rootNode->booleanNode('sidebar_mini')->defaultValue(true)->info('Show a little sidebar bar when the sidebar is collapsed, otherwise the sidebar is hidden.');
        $rootNode->booleanNode('collapsed_sidebar')->defaultValue(false)->info('Have a collapsed sidebar upon loading.');

        $rootNode->scalarNode('icheck')->defaultValue('square-blue')->info('Set the theme to use or false to disable');

        $this->createAssetConfig(
            $rootNode,
            [
                'admin_lte_css' => 'bundles/ynloadmin/vendor/admin-lte/css/AdminLTE.min.css',
                'admin_lte_skins_css' => 'bundles/ynloadmin/vendor/admin-lte/css/skins/_all-skins.min.css',
                'admin_lte_js' => 'bundles/ynloadmin/vendor/admin-lte/js/app.min.js',
                'jquery_slim_scroll_js' => 'bundles/ynloadmin/vendor/admin-lte/plugins/slimScroll/jquery.slimscroll.min.js',
                //'fastclick_js' => 'bundles/ynloadmin/vendor/admin-lte/plugins/fastclick/fastclick.min.js',
                'jquery_confirm_exit_js' => 'bundles/sonataadmin/jquery/jquery.confirmExit.js',
                'icheck_js' => 'bundles/ynloadmin/vendor/admin-lte/plugins/iCheck/icheck.min.js',
                'icheck_theme_css' => 'bundles/ynloadmin/vendor/admin-lte/plugins/iCheck/square/blue.css',
                'bootstrap_editable_js' => 'bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js',
                'bootstrap_editable_css' => 'bundles/sonataadmin/vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css',
                'jquery_ui_js' => 'bundles/sonataadmin/vendor/jqueryui/ui/minified/jquery-ui.min.js',
                'jquery_ui_css' => 'bundles/sonataadmin/vendor/jqueryui/themes/base/jquery-ui.css',
                'tree_view_js' => 'bundles/sonataadmin/treeview.js',
                'jquery_waypoints_js' => 'bundles/sonataadmin/vendor/waypoints/lib/jquery.waypoints.min.js',
                'jquery_waypoints_sticky_js' => 'bundles/sonataadmin/vendor/waypoints/lib/shortcuts/sticky.min.js',
                'sonata_admin_js' => 'bundles/sonataadmin/Admin.js',
            ]
        );

        return $treeBuilder;
    }
}
