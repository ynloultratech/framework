<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;

class AutocompleteContextManager implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    const SESSION_PREFIX = '_autocomplete_context_';

    /**
     * @var Session
     */
    protected $session;

    /**
     * AutocompleteContextManager constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param AutocompleteContextInterface $context
     *
     * @return string the context id for references
     */
    public function addContext(AutocompleteContextInterface $context)
    {
        $id = $this->getId($context);
        $this->session->set(self::SESSION_PREFIX . $id, $context);

        return $id;
    }

    /**
     * @param string $contextId
     *
     * @return AutocompleteContextInterface
     */
    public function getContext($contextId)
    {
        return $this->session->get(self::SESSION_PREFIX . $contextId);
    }

    /**
     * @param AutocompleteContextInterface $context
     *
     * @return string
     */
    private function getId(AutocompleteContextInterface $context)
    {
        return md5(serialize($context));
    }
}