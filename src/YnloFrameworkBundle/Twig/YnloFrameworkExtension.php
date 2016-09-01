<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Twig;

use YnloFramework\Component\ProgressBar\ProgressBar;

class YnloFrameworkExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('progressbar_widget', [$this, 'progressbarWidget'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    public function progressbarWidget(\Twig_Environment $env, ProgressBar $progressBar)
    {
        return $env->render('YnloFormBundle:ProgressBar:widget.html.twig', ['progressbar' => $progressBar]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ynlo_framework_extension';
    }
}
