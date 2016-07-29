<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface AutocompleteProviderInterface
{
    /**
     * Unique name of the provider for references
     *
     * @return string
     */
    public function getName();

    /**
     * Passing current request and auto-complete context
     * should return all available results
     *
     * @param Request                      $request
     * @param AutocompleteContextInterface $context
     *
     * @return AutocompleteResults
     */
    public function fetchResults(Request $request, AutocompleteContextInterface $context);

    /**
     * Build the http response to return the results to the frontend
     *
     * @param AutocompleteResults          $results
     * @param AutocompleteContextInterface $context
     *
     * @return Response
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context);
}