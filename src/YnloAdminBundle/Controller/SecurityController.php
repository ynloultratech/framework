<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * SecurityController.
 */
class SecurityController extends BaseSecurityController
{
    /**
     * @Route("/login", name="admin_login")
     * {@inheritdoc}
     */
    public function loginAction(Request $request)
    {
        /** @var UserInterface $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user instanceof UserInterface && ($user->hasRole('ROLE_ADMIN') || $user->isSuperAdmin())) {
            return new RedirectResponse($this->container->get('router')->generate('admin_dashboard'));
        }

        //clear error if the login don`t have any referer
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        if ($request->getSession() && !$request->headers->get('referer')) {
            $request->getSession()->set(Security::LAST_USERNAME, null);
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, null);
            $request->getSession()->set(Security::ACCESS_DENIED_ERROR, null);
        }

        return parent::loginAction($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderLogin(array $data)
    {
        return $this->render('YnloAdminBundle:Security:login.html.twig', $data);
    }
}
