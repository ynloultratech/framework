<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * NavbarLeftMenuBlock.
 */
class NavbarLeftMenuBlock extends TemplateBlock
{
    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'template' => 'YnloAdminBundle::Block/navbar_left_menu.html.twig',
            ]
        );
    }
}
