<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Symfony\Component\HttpFoundation\Response;
use YnloFramework\YnloAdminBundle\Admin\AbstractAdmin;
use YnloFramework\YnloModalBundle\Controller\ModalControllerTrait;
use YnloFramework\YnloModalBundle\Response\AjaxRefreshResponse;

/**
 * @property AbstractAdmin $admin
 */
class CRUDController extends BaseCRUDController
{
    use ModalControllerTrait;

    /**
     * {@inheritDoc}
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        $action = $parameters['action'];
        if ($this->admin->isActionOnModal($action)) {
            $parameters['base_template'] = $this->admin->getTemplate('ajax');
            $parameters['admin_pool'] = $this->get('sonata.admin.pool');
            $parameters['admin'] = $this->admin;

            $modal = $this->createModal($view, $parameters, $this->admin->getClassnameLabel(), $this->admin->getIcon());
            $this->admin->configureModal($action, $modal);

            return $this->renderModal($modal);
        }

        return parent::render($view, $parameters, $response);
    }

    /**
     * {@inheritDoc}
     */
    public function renderJson($data, $status = 200, $headers = [])
    {
        if ($this->isModalRequest()) {
            return new AjaxRefreshResponse();
        }

        return parent::renderJson($data, $status, $headers);
    }
}