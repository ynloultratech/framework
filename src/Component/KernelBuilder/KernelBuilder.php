<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\Component\KernelBuilder;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;
use YnloFramework\Component\KernelBuilder\Exception\MissingBundleException;
use YnloFramework\Component\KernelBuilder\Exception\MissingPackageException;

/**
 * Class KernelBuilder.
 */
class KernelBuilder
{
    use WithYnloBundlesTrait;
    use WithVendorBundlesTrait;

    /**
     * @var array
     */
    protected $bundles = [];

    /**
     * @var array
     */
    protected $devEvironments = ['test', 'dev'];

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * KernelBuilder constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Register all required bundles to create basic YnloFramework applications
     *
     * @return $this
     */
    public function basicApplication()
    {
        $this->addReference('Symfony\Bundle\FrameworkBundle\FrameworkBundle', 'symfony/smfony', null, 255);
        $this->addReference('Symfony\Bundle\SecurityBundle\SecurityBundle', 'symfony/smfony', null, 250);
        $this->addReference('Symfony\Bundle\TwigBundle\TwigBundle', 'symfony/smfony', null, 250);
        $this->addReference('Symfony\Bundle\AsseticBundle\AsseticBundle', 'symfony/assetic-bundle', null, 250);
        $this->addReference('Symfony\Bundle\MonologBundle\MonologBundle', 'symfony/monolog-bundle', null, 250);
        $this->addReference('YnloFramework\YnloFrameworkBundle\YnloFrameworkBundle', null, null, 250);
        $this->addReference('YnloFramework\YnloAssetsBundle\YnloAssetsBundle', null, null, 250);
        $this->addReference('Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle', 'sensio/framework-extra-bundle', null, 250);

        //auto-register app bundle
        if (class_exists('AppBundle\AppBundle')) {
            $this->addReference('AppBundle\AppBundle');
        }

        //dev
        $this->addReference('Symfony\Bundle\DebugBundle\DebugBundle', 'symfony/symfony', $this->devEvironments, -255);
        $this->addReference('Symfony\Bundle\WebProfilerBundle\WebProfilerBundle', 'symfony/symfony', $this->devEvironments, -255);
        $this->addReference('Sensio\Bundle\DistributionBundle\SensioDistributionBundle', 'sensio/distribution-bundle', $this->devEvironments, -255);
        $this->addReference('Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle', 'sensio/generator-bundle', $this->devEvironments, -255);

        return $this;
    }

    /**
     * Set array of environemnts used for deleopment purposes
     * used to add some bundles in this environments.
     *
     * @param array $devEvironments
     *
     * @return $this
     */
    public function setDevEvironments($devEvironments)
    {
        $this->devEvironments = $devEvironments;

        return $this;
    }

    /**
     * Add new dev environment
     *
     * @param string $env
     *
     * @return $this
     */
    public function addDevEnvironment($env)
    {
        $this->devEvironments[] = $env;

        return $this;
    }

    /**
     * Exclude some bundle in some environments
     *
     * @param string            $name
     * @param string|array|null $env enviroments to exclude or null to always exclude
     */
    public function excludeBundle($name, $env = null)
    {
        if ($this->hasBundle($name) && $this->isCurrentEvironment($$env)) {
            unset($this->bundles[$name]);
        }
    }

    /**
     * Register all required bundles to create basic YnloFramework applications
     * with administration backend
     *
     * @return $this
     */
    public function basicApplicationWithAdmin()
    {
        $this->basicApplication()
            ->withDoctrine()
            ->withBootstrap()
            ->withExtraFormWidgets()
            ->withMenu()
            ->withPjax()
            ->withModals()
            ->withAdmin();

        return $this;
    }

    /**
     * addBundle.
     *
     * @param Bundle|BundleReferenceInterface $bundle
     * @param string|array|null               $env
     * @param int                             $priority
     *
     * @return $this
     */
    public function addBundle($bundle, $env = null, $priority = 0)
    {
        if ($this->isCurrentEvironment($env)) {
            if ($bundle instanceof Bundle || $bundle instanceof BundleReferenceInterface) {
                $this->bundles[$bundle->getName()] = [
                    'bundle' => $bundle,
                    'priority' => $priority,
                ];
            } else {
                throw new \InvalidArgumentException('Bundle parameter should be a valid bundle or reference.');
            }
        }

        return $this;
    }

    /**
     * Register multiple bundles
     *
     * @param array|Bundle[]|BundleReference[] $bundles
     * @param null                             $env
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
     * hasBundle
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasBundle($name)
    {
        return array_key_exists($name, $this->bundles);
    }

    /**
     * Clear all bundles and references
     *
     * @return $this
     */
    public function clear()
    {
        $this->bundles = [];

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
        $bundles = $this->bundles;

        usort(
            $bundles,
            function ($a, $b) {
                if ($a['priority'] === $b['priority']) {
                    return 0;
                }

                return ($a['priority'] > $b['priority']) ? -1 : 1;
            }
        );

        $bundles = array_column($bundles, 'bundle');
        foreach ($bundles as $bundle) {
            if (!$bundle instanceof Bundle && !$bundle instanceof BundleReferenceInterface) {
                throw new \InvalidArgumentException('Invalid bundle or reference given.');
            }
            if ($bundle instanceof BundleReferenceInterface) {
                if (($class = $bundle->getClass()) && class_exists($class)) {
                    $bundle = new $class();
                } else {
                    if ($package = $bundle->getPackage()) {
                        $missingPackages[] = $package;
                    } else {
                        throw new MissingBundleException($bundle->getName());
                    }
                }
            }
            if ($bundle instanceof Bundle) {
                $compiledBundles[$bundle->getName()] = $bundle;
            } elseif (!$bundle instanceof BundleReferenceInterface) {
                $msg = sprintf('The class \'%s\' is not a valid bundle to add to the kernel', get_class($bundle));
                throw new \InvalidArgumentException($msg);
            }
        }

        if ($missingPackages) {
            throw new MissingPackageException($missingPackages);
        }

        return $compiledBundles;
    }

    /**
     * Check if given enviroment is current environment
     *
     * @param string|array $env
     *
     * @return bool
     */
    protected function isCurrentEvironment($env)
    {
        return $env === null
        || $env === $this->kernel->getEnvironment()
        || (is_array($env) && in_array($this->kernel->getEnvironment(), $env));
    }

    /**
     * addReference
     *
     * @param string $class
     * @param string $package
     * @param null   $env
     * @param int    $priority
     */
    protected function addReference($class, $package = null, $env = null, $priority = 0)
    {
        $this->addBundle($this->makeReference($class, $package), $env, $priority);
    }

    /**
     * makeReference
     *
     * @param string $class
     * @param string $package
     *
     * @return BundleReference
     */
    protected function makeReference($class, $package = null)
    {
        $name = substr($class, strrpos($class, '\\') + 1);

        return new BundleReference($name, $class, $package);
    }
}
