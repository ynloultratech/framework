<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use YnloFramework\Component\ProgressBar\ProgressBar;
use YnloFramework\Component\ProgressBar\ProgressBarManager;

class ProgressBarValueResolver implements ArgumentValueResolverInterface
{
    private $progressbarManager;

    public function __construct(ProgressBarManager $progressbarManager)
    {
        $this->progressbarManager = $progressbarManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return is_subclass_of($argument->getType(), ProgressBar::class) && !empty($token = $request->query->get(ProgressBar::TOKEN_PLACEHOLDER)) && $this->progressbarManager->hasToken($token);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $progressBar = $this->progressbarManager->load($request->query->get(ProgressBar::TOKEN_PLACEHOLDER));
        $request->attributes->set(ProgressBar::ATTRIBUTE_PLACEHOLDER, $progressBar);

        yield $progressBar;
    }
}
