<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use YnloFramework\YnloAdminBundle\Event\ConfigureMenuEvent;

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
     * configureMenu.
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
