<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use YnloFramework\YnloAdminBundle\Event\ConfigureMenuEvent;

/**
 * Class NavbarRightUserMenu
 */
class NavbarRightUserMenu extends AbstractMenuBuilderListener
{
    protected $user;

    /**
     * NavbarRightUserMenu constructor.
     *
     * @param mixed $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * configureMenu
     *
     * @param ConfigureMenuEvent $event
     */
    public function configureMenu(ConfigureMenuEvent $event)
    {
        $event->getMenu()
            ->addChild('logout')
            ->setExtra('template', ['YnloAdminBundle::Menu/navbar_right_user_menu.html.twig', ['user' => $this->user]]);
    }
}
