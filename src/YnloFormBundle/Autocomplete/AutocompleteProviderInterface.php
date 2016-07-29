<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
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