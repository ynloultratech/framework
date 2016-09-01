<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\ProgressBar;

use Symfony\Component\HttpFoundation\Session\Session;

class ProgressBarManager
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Exists progressbar token.
     *
     * @param string $token
     *
     * @return bool
     */
    public function hasToken($token)
    {
        return isset($this->session->get(ProgressBar::ATTRIBUTE_PLACEHOLDER)[$token]);
    }

    /**
     * Load progress from session.
     *
     * @param string $token The progress token.
     *
     * @return ProgressBar|null
     */
    public function load($token)
    {
        $progressbarBag = $this->session->get(ProgressBar::ATTRIBUTE_PLACEHOLDER);

        if (!isset($progressbarBag[$token])) {
            return;
        }

        return $progressbarBag[$token];
    }

    /**
     * Save progress in session.
     *
     * @param ProgressBar $progressbar
     *
     * @return ProgressBarManager
     */
    public function save(ProgressBar $progressbar)
    {
        $progressbarBag = $this->session->get(ProgressBar::ATTRIBUTE_PLACEHOLDER);
        $progressbarBag[$progressbar->getToken()] = $progressbar;

        $this->session->set(ProgressBar::ATTRIBUTE_PLACEHOLDER, $progressbarBag);

        return $this;
    }

    /**
     * Remove a progress from session.
     *
     * @param string $token The progress token.
     *
     * @return ProgressBarManager
     */
    public function remove($token)
    {
        $progressbarBag = $this->session->get(ProgressBar::ATTRIBUTE_PLACEHOLDER);
        unset($progressbarBag[$token]);
        $this->session->set(ProgressBar::ATTRIBUTE_PLACEHOLDER, $progressbarBag);

        return $this;
    }

    /**
     * Clear all progress in session.
     *
     * @return ProgressBarManager
     */
    public function clear()
    {
        $this->session->remove(ProgressBar::ATTRIBUTE_PLACEHOLDER);

        return $this;
    }
}
