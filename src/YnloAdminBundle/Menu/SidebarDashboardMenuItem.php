<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;

/**
 * Class SidebarDashboardMenuItem
 */
class SidebarDashboardMenuItem
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
     * configureMenu
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
