<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use YnloFramework\YnloAdminBundle\Event\ConfigureMenuEvent;

/**
 * AbstractMenuBuilderListener.
 */
abstract class AbstractMenuBuilderListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * configureMenu.
     *
     * @param ConfigureMenuEvent $event
     */
    abstract public function configureMenu(ConfigureMenuEvent $event);

    /**
     * Check if some path is present in current request.
     *
     * @param string $path
     *
     * @return bool
     */
    protected function isInPath($path)
    {
        return strpos($this->getRequest()->getUri(), $path) !== false;
    }

    /**
     * Get current request.
     *
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

    /**
     * trans.
     *
     * @param       $id
     * @param array $parameters
     * @param null  $domain
     * @param null  $locale
     *
     * @return string
     */
    protected function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->container->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    /**
     * Check for permissions.
     *
     * @param      $attributes
     * @param null $object
     *
     * @return bool
     */
    protected function isGranted($attributes, $object = null)
    {
        return $this->container->get('security.authorization_checker')->isGranted($attributes, $object);
    }
}
