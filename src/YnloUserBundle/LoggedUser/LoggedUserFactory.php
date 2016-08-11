<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloUserBundle\LoggedUser;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * LoggedUserFactory
 */
class LoggedUserFactory
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * LoggedEmployeeFactory constructor.
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns a user representation.
     *
     * @return mixed Can be a UserInterface instance, an object implementing a __toString method,
     *               or the username as a regular string
     */
    public function getLoggedUser()
    {
        /** @var TokenInterface $token */
        if (!($token = $this->tokenStorage->getToken())) {
            return;
        }

        return $token->getUser();
    }
}
