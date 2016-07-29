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

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class TypeaheadProvider extends SimpleEntityProvider
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'typeahead';
    }

    /**
     * @inheritDoc
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context)
    {
        $array = [];

        $property = $context->getParameter('autocomplete');

        $accessor = new PropertyAccessor();

        foreach ($results as $id => $result) {
            if ($accessor->isReadable($result, $property)) {
                $array[] = $accessor->getValue($result, $property);
            }
        }

        return new JsonResponse($array);
    }
}