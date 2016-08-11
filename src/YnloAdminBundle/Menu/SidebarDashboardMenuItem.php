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
    protected $enabled;

    /**
     * SidebarDashboardMenuItem constructor.
     *
     * @param bool $enabled
     */
    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * configureMenu
     *
     * @param ConfigureMenuEvent $event
     */
    public function configureMenu(ConfigureMenuEvent $event)
    {
        if ($this->enabled) {
            $order = array_merge(['Dashboard'], array_keys($event->getMenu()->getChildren()));
            $event->getMenu()
                ->addChild('Dashboard', ['route' => 'admin_dashboard'])
                ->setExtra('icon', 'fa fa-tachometer fa-fw');
            $event->getMenu()->reorderChildren($order);
        }
    }
}
