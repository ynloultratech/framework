<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SidebarProfileBlock extends TemplateBlock
{
    /**
     * @param mixed $user user representation object
     */
    public function setUser($user)
    {
        $this->parameters['user'] = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'YnloAdminBundle::Block/sidebar_profile.html.twig',
        ]);
    }
}
