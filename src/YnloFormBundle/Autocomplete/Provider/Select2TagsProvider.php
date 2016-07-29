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

class Select2TagsProvider extends SimpleEntityProvider
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'select2_tags';
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
                $value = $accessor->getValue($result, $property);
                $array[] = [
                    'id' => $value,
                    'text' => $value,
                ];
            }
        }

        return new JsonResponse(
            [
                'results' => $array,
                'pagination' => [
                    'more' => $results->getTotalOverAll() > $results->count(),
                ]
            ]
        );
    }
}