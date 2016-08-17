<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use YnloFramework\YnloAdminBundle\Event\ConfigureMenuEvent;

/**
 * Class SidebarDashboardMenuItem.
 */
class SidebarDashboardMenuItem extends AbstractMenuBuilderListener
{
    protected $adminConfig;

    /**
     * SidebarDashboardMenuItem constructor.
     *
     * @param array $adminConfig
     */
    public function __construct($adminConfig = [])
    {
        $this->adminConfig = $adminConfig;
    }

    /**
     * configureMenu.
     *
     * @param ConfigureMenuEvent $event
     */
    public function configureMenu(ConfigureMenuEvent $event)
    {
        if ($this->adminConfig['menu']['main_dashboard']) {
            $order = array_merge(['Dashboard'], array_keys($event->getMenu()->getChildren()));
            $event->getMenu()
                ->addChild('Dashboard', ['route' => 'admin_dashboard'])
                ->setExtra('icon', $this->adminConfig['dashboard_icon']);
            $event->getMenu()->reorderChildren($order);
        }
    }
}
