<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
 */

namespace AdminBundle\Menu;

use Knp\Menu\ItemInterface;

/**
 * AdminMenuBuilderInterface.
 */
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
