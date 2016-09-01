<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use YnloFramework\Component\ProgressBar\ProgressBar;
use YnloFramework\Component\ProgressBar\ProgressBarManager;
use YnloFramework\Component\ProgressBar\StackProgressBar;

/**
 * This class manipulates requests and response in order to works with progressbar process.
 */
class ProgressBarListener
{
    private $progressbarManager;

    public function __construct(ProgressBarManager $progressbarManager)
    {
        $this->progressbarManager = $progressbarManager;
    }

    /**
     * Auto-start progressbar whether this request is a progress action
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()
            || !$request->isXmlHttpRequest()
            || null === $request->attributes->get(ProgressBar::ATTRIBUTE_PLACEHOLDER)
            || null === $progressbar = $this->progressbarManager->load($request->attributes->get(ProgressBar::ATTRIBUTE_PLACEHOLDER))
        ) {
            return;
        }

        if (!$progressbar->isStarted()) {
            $progressbar->start();
        }

        if (!$progressbar->isInProgress()) {
            $event->setResponse($this->createResponse($progressbar));
        }
    }

    /**
     * Auto-convert to JsonResponse whether the controller result is a ProgressBar instance.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        if (!$event->isMasterRequest() || !$request->isXmlHttpRequest() || !$result instanceof ProgressBar) {
            return;
        }

        $event->setResponse($this->createResponse($result));
    }

    private function createResponse(ProgressBar $progressbar)
    {
        $nextProgressbar = null;
        if ($progressbar->isFinished() || $progressbar->isCancelled()) {
            $this->progressbarManager->remove($progressbar->getToken());

            if ($progressbar instanceof StackProgressBar && $progressbar->getNextProgressbarToken()) {
                $nextProgressbar = $this->progressbarManager->load($progressbar->getNextProgressbarToken());
            }
        } else {
            $this->progressbarManager->save($progressbar);
        }

        return $progressbar->getJsonResponse($nextProgressbar);
    }
}
