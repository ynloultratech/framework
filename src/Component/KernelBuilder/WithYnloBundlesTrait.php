<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\KernelBuilder;

trait WithYnloBundlesTrait
{
    /**
     * Enable the administration.
     *
     * @return $this
     */
    public function withAdmin()
    {
        if (!$this->hasBundle('FOSUserBundle')) {
            throw new \LogicException('The administration require user login support. Please enable the user bundle (->withUsers) before enable the administration.');
        }
        $this->addReference('Sonata\CoreBundle\SonataCoreBundle', 'sonata-project/core-bundle');
        $this->addReference('Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle', 'sonata-project/doctrine-orm-admin-bundle');
        $this->addReference('Sonata\AdminBundle\SonataAdminBundle', 'sonata-project/admin-bundle');
        $this->addReference('Sonata\BlockBundle\SonataBlockBundle', 'sonata-project/block-bundle');

        $this->withBootstrap();
        $this->withMenu();

        $this->addReference('YnloFramework\YnloAdminBundle\YnloAdminBundle');

        return $this;
    }

    /**
     * Enable user login support.
     *
     * @return $this
     */
    public function withUsers($userClass, $groupClass = null)
    {
        self::$userClass = $userClass;
        self::$groupClass = $groupClass;
        $this->addReference('FOS\UserBundle\FOSUserBundle', 'friendsofsymfony/user-bundle');
        $this->addReference('YnloFramework\YnloUserBundle\YnloUserBundle');
        $this->withMail();

        return $this;
    }

    /**
     * Enable extra form extensions and Widgets.
     *
     * @return $this
     */
    public function withExtraFormWidgets()
    {
        $this->addReference('YnloFramework\YnloFormBundle\YnloFormBundle');

        return $this;
    }

    /**
     * Enable pjax navigation.
     *
     * @return $this
     */
    public function withPjax()
    {
        $this->addReference('YnloFramework\YnloPjaxBundle\YnloPjaxBundle');

        return $this;
    }

    /**
     * Enable modal windows.
     *
     * @return $this
     */
    public function withModals()
    {
        $this->addReference('YnloFramework\YnloModalBundle\YnloModalBundle');

        return $this;
    }
}
