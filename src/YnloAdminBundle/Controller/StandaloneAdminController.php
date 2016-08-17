<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller can be used to render admin actions
 * in controllers not related with specific entity administration.
 */
class StandaloneAdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected function render($view, array $parameters = [], Response $response = null)
    {
        $pool = $this->get('sonata.admin.pool');
        $parameters['admin_pool'] = (isset($parameters['admin_pool'])) ? $parameters['admin_pool'] : $pool;

        $parameters['base_template'] = 'YnloAdminBundle::standard_layout.html.twig';

        return parent::render($view, $parameters, $response);
    }
}
