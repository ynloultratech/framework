<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloUserBundle\LoggedUser;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * LoggedUserFactory.
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
