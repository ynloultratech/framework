<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use YnloFramework\YnloAdminBundle\Admin\AbstractAdmin;
use YnloFramework\YnloAdminBundle\Event\ConfigureMenuEvent;

/**
 * Class Builder.
 */
class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * mainMenu.
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $adminConfig = $this->container->getParameter('ynlo_admin_config');

        if ($adminConfig['menu']['main_admins']) {
            $menu = $this->container->get('sonata.admin.menu_builder')->createSidebarMenu();
            $this->resolveAdminIcons($menu);
        } else {
            $menu = $factory->createItem('root', $options);
        }

        $event = new ConfigureMenuEvent($factory, $menu);
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::SIDEBAR, $event);

        $menu->setChildrenAttribute('class', $adminConfig['menu']['main_menu_class']);

        if ($adminConfig['menu']['main_navigation_header']) {
            $order = array_merge(['Main Navigation'], array_keys($menu->getChildren()));
            $menu->addChild('Main Navigation');
            $menu->reorderChildren($order);
        }

        return $menu;
    }

    /**
     * navbarLeftMenu.
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function navbarLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', $options);
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $event = new ConfigureMenuEvent($factory, $menu);
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::NAVBAR_LEFT, $event);

        return $menu;
    }

    /**
     * navbarLeftMenu.
     *
     * @param FactoryInterface $factory
     * @param array            $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function navbarRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root', $options);
        $menu->setChildrenAttribute('class', 'nav navbar-nav pull-right');

        $event = new ConfigureMenuEvent($factory, $menu);
        $this->container->get('event_dispatcher')->dispatch(ConfigureMenuEvent::NAVBAR_RIGHT, $event);

        return $menu;
    }

    /**
     * resolveAdminIcons.
     *
     * @param ItemInterface $menu
     */
    protected function resolveAdminIcons(ItemInterface $menu)
    {
        foreach ($menu->getChildren() as $child) {
            if (!$child->getExtra('icon') && $child->hasChildren()) {
                $child->setExtra('icon', 'fa fa-folder');
            }
            if ($child->hasChildren()) {
                foreach ($child->getChildren() as $subChild) {
                    if (!$subChild->getExtra('icon') && ($admin = $subChild->getExtra('admin'))) {
                        if ($admin instanceof AbstractAdmin) {
                            $subChild->setExtra('icon', $admin->getIcon());
                        }
                    }
                }
            }
        }
    }
}
