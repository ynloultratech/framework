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

namespace YnloFramework\YnloPjaxBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class PjaxListener
 *
 * This class manipulates requests and response in order to works with Pjax
 */
class PjaxListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * onKernelRequest
     *
     * Manipulate the request in order to distinguish ajax requests and pjax requests
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$request->headers->get('x-pjax') || !$request->isXmlHttpRequest()) {
            return;
        }

        if ($this->container->hasParameter('ynlo.pjax.config')) {
            $config = $this->container->getParameter('ynlo.pjax.config');
            if (array_key_value($config, 'remove_ajax_header')) {
                //convert pjax request to non ajax requests
                $request->headers->remove('X-Requested-With');
            }
        }
    }

    /**
     * onKernelResponse
     *
     * Manipulate the response and add the some headers
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->headers->get('x-pjax')) {
            $baseUrl = $request->getBaseUrl();
            $pathInfo = $request->getPathInfo();
            $query = http_build_query($request->query->getIterator());
            $url = $baseUrl . $pathInfo . (($query) ? '?' . $query : '');

            $responseHeaders = $event->getResponse()->headers;
            $responseHeaders->set('X-PJAX-URL', $url);
        }
    }
}
