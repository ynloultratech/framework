<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\ProgressBar;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProgressBar
{
    const TOKEN_PLACEHOLDER = '_progressbar_token';
    const ATTRIBUTE_PLACEHOLDER = '_progressbar';

    const STATUS_NONE = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FINISHED = 2;
    const STATUS_CANCELLED = 3;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $step = 0;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var int
     */
    protected $startTime = null;

    /**
     * @var float
     */
    protected $percent = 0.0;

    /**
     * @var string
     */
    protected $progressRoute;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * @var string
     */
    protected $status = self::STATUS_NONE;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $messageTemplate = 'Processed %s records of %s.';

    /**
     * Progress constructor.
     *
     * @param int $max Maximum steps (0 if unknown)
     */
    public function __construct($max = 0)
    {
        $this->setMaxSteps($max);
        $this->token = md5(date('d.m.Y H:i:s').mt_rand(0, 1000000000));
        $this->startTime = time();
    }

    /**
     * Starts the progress output.
     *
     * @param int|null $max Number of steps to complete the bar (0 if indeterminate), null to leave unchanged
     */
    public function start($max = null)
    {
        $this->startTime = time();
        $this->step = 0;
        $this->percent = 0.0;

        if (null !== $max) {
            $this->setMaxSteps($max);
        }

        $this->setStatus(self::STATUS_IN_PROGRESS);
    }

    /**
     * Advances the progress X steps.
     *
     * @param int $step Number of steps to advance
     *
     * @throws \LogicException
     */
    public function advance($step = 1)
    {
        $this->setProgress($this->step + $step);
    }

    /**
     * Stop the progress and redirect to given url.
     *
     * @param null $redirectUrl
     */
    public function stop($redirectUrl = null)
    {
        if ($redirectUrl) {
            $this->setRedirectUrl($redirectUrl);
        }

        $this->setStatus(self::STATUS_CANCELLED);
    }

    /**
     * Finishes the progress.
     */
    public function finish()
    {
        if (!$this->max) {
            $this->max = $this->step;
        }

        $this->setProgress($this->max);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the current progress.
     *
     * @param int $step The current progress
     *
     * @throws \LogicException
     */
    public function setProgress($step)
    {
        $step = (int) $step;
        if ($step < $this->step) {
            throw new \LogicException('You can\'t regress the progress bar.');
        }

        if ($this->max && $step >= $this->max) {
            $step = $this->max;
            $this->setStatus(self::STATUS_FINISHED);
        }

        $this->step = $step;
        $this->percent = $this->max ? round($this->step * 100 / $this->max) : 0;
    }

    /**
     * Gets the progress bar maximal steps.
     *
     * @return int The progress bar max steps
     */
    public function getMaxSteps()
    {
        return $this->max;
    }

    /**
     * Gets the progress bar start time.
     *
     * @return int The progress bar start time
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param int $startTime
     *
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Gets the current step position.
     *
     * @return int The progress bar step
     */
    public function getProgress()
    {
        return $this->step;
    }

    /**
     * @return string
     */
    public function getProgressRoute()
    {
        return $this->progressRoute;
    }

    /**
     * @param string $route The name of the route
     *
     * @return $this
     */
    public function setProgressRoute($route)
    {
        $this->progressRoute = $route;

        return $this;
    }

    /**
     * Gets the progress route parameters.
     *
     * @return array
     */
    public function getProgressRouteParameters()
    {
        return [self::TOKEN_PLACEHOLDER => $this->token];
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     *
     * @return $this
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Gets the current progress bar percent.
     *
     * @return float The current progress bar percent
     */
    public function getProgressPercent()
    {
        return $this->percent;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->status !== self::STATUS_NONE;
    }

    /**
     * @return bool
     */
    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getRemaining()
    {
        if (!$this->getMaxSteps()) {
            return '???';
        }

        if (!$this->getProgress()) {
            $remaining = 0;
        } else {
            $remaining = round((time() - $this->getStartTime()) / $this->getProgress() * ($this->getMaxSteps() - $this->getProgress()));
        }

        return Helper::formatTime($remaining);
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return ProgressBar
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }

    /**
     * @param string $messageTemplate
     *
     * @return $this
     */
    public function setMessageTemplate($messageTemplate)
    {
        $this->messageTemplate = $messageTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return sprintf($this->getMessageTemplate(), $this->getProgress(), $this->getMaxSteps());
    }

    /**
     * @param ProgressBar|null $next
     *
     * @return JsonResponse
     */
    public function getJsonResponse(ProgressBar $next = null)
    {
        return new JsonResponse($this->getDataArray($next));
    }

    /**
     * @param ProgressBar|null $next
     *
     * @return array
     */
    private function getDataArray(ProgressBar $next = null)
    {
        return [
            'token' => $this->getToken(),
            'url' => $this->getProgressRoute(),
            'status' => $this->status,
            'percentage' => $this->getProgressPercent(),
            'remaining' => $this->getRemaining(),
            'max' => $this->getMaxSteps(),
            'progress' => $this->getProgress(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'redirectUrl' => $this->getRedirectUrl(),
            'next' => $next ? $next->getDataArray() : null,
        ];
    }

    /**
     * Sets the progress bar maximal steps.
     *
     * @param int $max The progress bar max steps
     */
    private function setMaxSteps($max)
    {
        $this->max = max(0, (int) $max);
    }
}
