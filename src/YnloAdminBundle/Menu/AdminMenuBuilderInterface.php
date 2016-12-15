<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use Knp\Menu\ItemInterface;

interface AdminMenuBuilderInterface
{
    /**
     * buildMenu.
     *
     * @param ItemInterface $menu root menu node to build the menu
     *
     * @return mixed
     */
    public function buildMenu(ItemInterface $menu);
}
