<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;

class Select2TagsProvider extends SimpleEntityProvider
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'select2_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context)
    {
        $accessor = new PropertyAccessor();

        $data['results'] = [];
        $property = $context->getParameter('autocomplete');

        foreach ($results as $id => $result) {
            if ($accessor->isReadable($result, $property)) {
                $value = $accessor->getValue($result, $property);
                $data['results'][] = [
                    'id' => $value,
                    'text' => $value,
                ];
            }
        }

        if ($max = $context->getParameter('max_results', false)) {
            $data['pagination'] = [
                'more' => $results->count() == $max,
            ];
        }

        return new JsonResponse($data);
    }
}
