<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\ProgressBar;

class StackProgressBar extends ProgressBar
{
    /**
     * @var string
     */
    private $nextProgressbarToken;

    /**
     * Set next progressbar token.
     *
     * @param string $nextProgressbarToken
     *
     * @return StackProgressBar
     */
    public function setNextProgressbarToken($nextProgressbarToken)
    {
        $this->nextProgressbarToken = $nextProgressbarToken;

        return $this;
    }

    /**
     * Get next progressbar token.
     *
     * @return string
     */
    public function getNextProgressbarToken()
    {
        return $this->nextProgressbarToken;
    }

    /**
     * Set next progressbar.
     *
     * @param ProgressBar $progressbar
     *
     * @return StackProgressBar
     */
    public function setNext(ProgressBar $progressbar)
    {
        $this->nextProgressbarToken = $progressbar->getToken();

        return $this;
    }
}
