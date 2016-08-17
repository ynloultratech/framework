<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Event;

use Sonata\AdminBundle\Event\ConfigureMenuEvent as AbstractConfigureMenuEvent;

/**
 * ConfigureMenuEvent.
 */
class ConfigureMenuEvent extends AbstractConfigureMenuEvent
{
    const SIDEBAR = 'admin.event.configure.menu.sidebar';
    const NAVBAR_LEFT = 'admin.event.configure.menu.navbar_left';
    const NAVBAR_RIGHT = 'admin.event.configure.menu.navbar_right';
}
