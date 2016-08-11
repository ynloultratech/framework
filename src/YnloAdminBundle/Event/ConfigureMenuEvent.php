<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloAdminBundle\Event;

use Sonata\AdminBundle\Event\ConfigureMenuEvent as AbstractConfigureMenuEvent;

/**
 * ConfigureMenuEvent
 */
class ConfigureMenuEvent extends AbstractConfigureMenuEvent
{
    const SIDEBAR =  'admin.event.configure.menu.sidebar';
    const NAVBAR_LEFT =  'admin.event.configure.menu.navbar_left';
    const NAVBAR_RIGHT =  'admin.event.configure.menu.navbar_right';
}
