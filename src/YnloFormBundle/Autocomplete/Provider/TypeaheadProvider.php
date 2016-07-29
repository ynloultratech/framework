<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class TypeaheadProvider extends SimpleEntityProvider
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'typeahead';
    }

    /**
     * {@inheritdoc}
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
