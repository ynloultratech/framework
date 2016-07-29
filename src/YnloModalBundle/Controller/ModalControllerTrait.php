<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Router;
use YnloFramework\YnloModalBundle\Response\AjaxErrorResponse;
use YnloFramework\YnloModalBundle\Response\AjaxRedirectResponse;
use YnloFramework\YnloModalBundle\Response\AjaxRefreshResponse;
use YnloFramework\YnloModalBundle\Response\AjaxSuccessResponse;
use YnloFramework\YnloModalBundle\Modal\Modal;
use YnloFramework\YnloModalBundle\Response\ModalResponse;

trait ModalControllerTrait
{
    /**
     * Ajax redirect response is helpful for modals to redirect
     * to another location after modal is submitted.
     *
     * @param string $route           #Route
     * @param array  $routeParameters
     *
     * @return AjaxRedirectResponse
     *
     * @throws InvalidParameterException
     * @throws MissingMandatoryParametersException
     * @throws RouteNotFoundException
     */
    public function ajaxRedirect($route, array $routeParameters = [])
    {
        /** @var Router $router */
        $router = $this->get('router');

        return new AjaxRedirectResponse($router->generate($route, $routeParameters));
    }

    /**
     * Ajax redirect response is helpful for modals to refresh current
     * location after modal is submitted.
     *
     * @return AjaxRedirectResponse
     */
    public function ajaxRefresh()
    {
        return new AjaxRefreshResponse();
    }

    /**
     * Ajax success response is helpful to get a json response with some parameters
     * is used in modals to know when some action is success.
     *
     * @param array $parameters
     *
     * @return AjaxSuccessResponse
     */
    public function ajaxSuccess(array $parameters = [])
    {
        return new AjaxSuccessResponse($parameters);
    }

    /**
     * Ajax error response is helpful to return a simple json response with error message.
     *
     * @param string $message
     *
     * @return AjaxErrorResponse
     */
    public function ajaxError($message = null)
    {
        return new AjaxErrorResponse($message);
    }

    /**
     * Create simple modal with given view and parameters.
     *
     * @param string|FormView|FormInterface $view       view or form to render
     * @param array                         $parameters
     * @param string                        $title
     *
     * @return Modal
     */
    public function createModal($view, array $parameters = [], $title = null)
    {
        if ($view instanceof FormView || $view instanceof FormInterface) {
            if ($view instanceof FormInterface) {
                $view = $view->createView();
            }
            $message = $this->renderView($this->getParameter('ynlo.modal.config')['modal_form']['template'], ['form' => $view]);
        } elseif (is_string($view)) {
            $message = $this->renderView($view, $parameters);
        } else {
            throw new \InvalidArgumentException('Invalid view, should be a valid view or form.');
        }

        return new Modal($message, $title);
    }

    /**
     * Create simple modal for given form, a predefined template will be used.
     *
     * @param FormView|FormInterface $form
     * @param string                 $title   modal title
     * @param string                 $icon    icon class to use
     * @param bool                   $buttons automatically add basic buttons, cancel & submit
     *
     * @return Modal
     */
    public function createModalForm($form, $title = null, $icon = null, $buttons = true)
    {
        $modal = $this->createModal($form, [], $title, $icon);

        if ($buttons) {
            $config = $this->getParameter('ynlo.modal.config');
            foreach ($config['modal_form']['buttons'] as $id => $button) {
                if ($this->has('translator')) {
                    $label = $this->get('translator')->trans($button['label'], [], $button['translation_domain']);
                } else {
                    $label = $button['label'];
                }

                $modal->createButton($id, $label, $button['action'], $button['class'], $button['icon']);
            }
        }

        return $modal;
    }

    /**
     * Render already created template and show in the browser.
     *
     * @param Modal|string $modal
     *
     * @return ModalResponse
     */
    public function renderModal(Modal $modal)
    {
        return new ModalResponse($modal);
    }
}
