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
use YnloFramework\YnloModalBundle\Response\AjaxRedirectResponse;
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
        if ($this->isModalAction($action)) {
            $parameters['base_template'] = 'YnloAdminBundle::modal_layout.html.twig';
            $parameters['admin_pool'] = $this->get('sonata.admin.pool');
            $parameters['admin'] = $this->admin;

            //By default, Pjax remove "X-Requested-With" header
            //but in some cases the modal is returned as Modal response,
            //Pjax plugin show this type of response using a modal, then is required restart the X-Requested-With header
            //to hide some contents in the view
            $this->getRequest()->headers->set('X-Requested-With', 'XMLHttpRequest');

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
            if ($this->getRequest()->getMethod() === 'DELETE') {
                return new AjaxRedirectResponse($this->admin->generateUrl('list'));
            }

            return new AjaxRefreshResponse();
        }

        return parent::renderJson($data, $status, $headers);
    }

    /**
     * Verify if some action should be loaded in a modal.
     *
     * @param $action
     *
     * @return bool
     */
    protected function isModalAction($action)
    {
        $ajax = ($this->isXmlHttpRequest() || $this->isModalRequest() || $this->getRequest()->headers->get('X-Pjax'));

        return $ajax && $this->admin->isActionOnModal($action);
    }
}