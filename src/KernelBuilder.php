<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use YnloFramework\Exception\MissingBundleException;
use YnloFramework\Exception\MissingPackageException;

/**
 * Class KernelBuilder
 */
class KernelBuilder
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var array
     */
    private $bundles = [];

    /**
     * @var array
     */
    private $knownBundlePackages
        = [
            'Symfony\Bundle\FrameworkBundle\FrameworkBundle' => 'symfony/symfony',
            'Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle' => 'sensio/framework-extra-bundle',
            'Symfony\Bundle\SecurityBundle\SecurityBundle' => 'symfony/symfony',
            'Symfony\Bundle\TwigBundle\TwigBundle' => 'symfony/symfony',
            'Symfony\Bundle\AsseticBundle\AsseticBundle' => 'symfony/assetic-bundle',
            'Symfony\Bundle\MonologBundle\MonologBundle' => 'symfony/monolog-bundle',
            'Symfony\Bundle\WebProfilerBundle\WebProfilerBundle' => 'symfony/symfony',
            'Symfony\Bundle\DebugBundle\DebugBundle' => 'symfony/symfony',
            'Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle' => 'sonata-project/doctrine-orm-admin-bundle',
            'Sonata\AdminBundle\SonataAdminBundle' => 'sonata-project/admin-bundle',
            'Sonata\CoreBundle\SonataCoreBundle' => 'sonata-project/core-bundle',
            'Sonata\BlockBundle\SonataBlockBundle' => 'sonata-project/block-bundle',
            'Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle' => 'mopa/bootstrap-bundle',
            'Knp\Bundle\MenuBundle\KnpMenuBundle' => 'knplabs/knp-menu-bundle',
            'Sensio\Bundle\DistributionBundle\SensioDistributionBundle' => 'sensio/distribution-bundle',
            'Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle' => 'sensio/generator-bundle',
            'FOS\UserBundle\FOSUserBundle' => 'friendsofsymfony/user-bundle',
        ];

    /**
     * KernelBuilder constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
        $this
            ->addBundles(
                [
                    'Symfony\Bundle\FrameworkBundle\FrameworkBundle',
                    'Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle',
                    'Symfony\Bundle\SecurityBundle\SecurityBundle',
                    'Symfony\Bundle\TwigBundle\TwigBundle',
                    'Symfony\Bundle\AsseticBundle\AsseticBundle',
                    'YnloFramework\YnloFrameworkBundle\YnloFrameworkBundle',
                    'YnloFramework\YnloAssetsBundle\YnloAssetsBundle',
                    'Symfony\Bundle\MonologBundle\MonologBundle',
                ]
            )
            ->addBundles(
                [
                    'Symfony\Bundle\WebProfilerBundle\WebProfilerBundle',
                    'Symfony\Bundle\DebugBundle\DebugBundle',
                    'Sensio\Bundle\DistributionBundle\SensioDistributionBundle',
                    'Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle',
                ],
                [
                    'dev',
                    'test',
                ]
            );

        //auto-register app bundle
        if (class_exists('AppBundle\AppBundle')) {
            $this->addBundle('AppBundle\AppBundle');
        }
    }

    /**
     * Prepare the kernel to setup
     *
     * @param Kernel $kernel
     *
     * @return KernelBuilder
     */
    public static function setUp(Kernel $kernel)
    {
        return new self($kernel);
    }

    /**
     * Clear the builder to customize bundles
     *
     * @return $this
     */
    public function clear()
    {
        $this->bundles = [];

        return $this;
    }

    /**
     * Unregister the automatic loaded AppBundle
     *
     * @return $this
     */
    public function notAppBundle()
    {
        if (isset($this->bundles['AppBundle\AppBundle'])) {
            unset($this->bundles['AppBundle\AppBundle']);
        }

        return $this;
    }

    /**
     * Register all required bundles to create basic YnloFramework applications
     *
     * @return $this
     */
    public function basicApplication()
    {
        $this
            ->withBootstrap()
            ->withMenu()
            ->withExtraFormWidgets()
            ->withPjax();

        return $this;
    }

    /**
     * Register all required bundles to create basic YnloFramework applications
     * with administration backend
     *
     * @return $this
     */
    public function basicApplicationWithAdmin()
    {
        $this->withAdmin()
            ->withDoctrine()
            ->withExtraFormWidgets()
            ->withPjax();

        return $this;
    }

    /**
     * Support to create users and login in the application
     *
     * @return $this
     */
    public function withUsers()
    {
        $this->addBundle('FOS\UserBundle\FOSUserBundle');
    }

    /**
     * Enable doctrine support
     *
     * @return $this
     */
    public function withDoctrine()
    {
        $this->addBundle('Doctrine\Bundle\DoctrineBundle\DoctrineBundle');

        return $this;
    }

    /**
     * Enable the administration
     *
     * @return $this
     */
    public function withAdmin()
    {
        $this->addBundle('Sonata\CoreBundle\SonataCoreBundle');
        $this->addBundle('Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle');
        $this->addBundle('Sonata\AdminBundle\SonataAdminBundle');
        $this->addBundle('Sonata\BlockBundle\SonataBlockBundle');

        $this->withBootstrap();
        $this->withMenu();
        $this->withUsers();

        $this->addBundle('YnloFramework\YnloAdminBundle\YnloAdminBundle');

        return $this;
    }

    /**
     * Enable extra form extensions and Widgets
     *
     * @return $this
     */
    public function withExtraFormWidgets()
    {
        $this->addBundle('YnloFramework\YnloFormBundle\YnloFormBundle');

        return $this;
    }

    /**
     * Enable pjax navigation
     *
     * @return $this
     */
    public function withPjax()
    {
        $this->addBundle('YnloFramework\YnloPjaxBundle\YnloPjaxBundle');

        return $this;
    }

    /**
     * Enable bootstrap support with MopaBootstrap
     *
     * @return $this
     */
    public function withBootstrap()
    {
        $this->addBundle('Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle');

        return $this;
    }

    /**
     * bootstrap
     *
     * @return $this
     */
    public function withMenu()
    {
        $this->addBundle('Knp\Bundle\MenuBundle\KnpMenuBundle');

        return $this;
    }

    /**
     * addBundle
     *
     * @param Bundle|string     $bundle
     * @param string|array|null $env
     * @param array             $arguments
     *
     * @return $this
     */
    public function addBundle($bundle, $env = null, $arguments = [])
    {
        if ($env === null
            || $env === $this->kernel->getEnvironment()
            || (is_array($env) && in_array($this->kernel->getEnvironment(), $env))
        ) {
            if (is_string($bundle)) {
                $this->bundles[$bundle] = $bundle;
            } else {
                $this->bundles[get_class($bundle)] = $bundle;
            }
        }

        return $this;
    }

    /**
     * Register multiple bundles
     *
     * @param array $bundles
     * @param null  $env
     *
     * @return $this;
     */
    public function addBundles(array $bundles, $env = null)
    {
        foreach ($bundles as $bundle) {
            $this->addBundle($bundle, $env);
        }

        return $this;
    }

    /**
     * Return the array of bundles according to your preferences
     *
     * @return array|Bundle[]
     *
     * @throws MissingBundleException
     * @throws MissingPackageException
     */
    public function build()
    {
        $compiledBundles = [];
        $missingPackages = [];
        foreach ($this->bundles as $bundle) {
            if (is_string($bundle)) {
                if (class_exists($bundle)) {
                    $bundle = new $bundle();
                } else {
                    if (isset($this->knownBundlePackages[$bundle])) {
                        $missingPackages[] = $this->knownBundlePackages[$bundle];
                    } else {
                        throw new MissingBundleException($bundle);
                    }
                }
            }
            $compiledBundles[] = $bundle;
        }

        if ($missingPackages) {
            throw new MissingPackageException($missingPackages);
        }

        return $compiledBundles;
    }
}
