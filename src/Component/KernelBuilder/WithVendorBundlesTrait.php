<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\Component\KernelBuilder;

/**
 * Class WithVendorBundlesTrait
 */
trait WithVendorBundlesTrait
{
    /** @var string */
    public static $userClass;

    /** @var string */
    public static $groupClass;

    /**
     * Enable mail support.
     *
     * @return $this
     */
    public function withMail()
    {
        $this->addReference('Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle', 'symfony/swiftmailer-bundle');

        return $this;
    }

    /**
     * Enable bootstrap support with MopaBootstrap
     *
     * @return $this
     */
    public function withBootstrap()
    {
        $this->addReference('Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle', 'mopa/bootstrap-bundle');

        return $this;
    }

    /**
     * Enable doctrine support.
     *
     * @return $this
     */
    public function withDoctrine()
    {
        $this->addReference('Doctrine\Bundle\DoctrineBundle\DoctrineBundle', 'doctrine/doctrine-bundle');

        return $this;
    }

    /**
     * bootstrap
     *
     * @return $this
     */
    public function withMenu()
    {
        $this->addReference('Knp\Bundle\MenuBundle\KnpMenuBundle', 'knplabs/knp-menu-bundle');

        return $this;
    }
}
