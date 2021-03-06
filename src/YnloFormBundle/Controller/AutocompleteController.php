<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteProviderInterface;

class AutocompleteController extends Controller
{
    public function autoCompleteAction(Request $request)
    {
        $context = $this->get('ynlo.form.autocomplete.context_manager')->getContext($request->get('_context'));

        if (!$context || !($context instanceof AutocompleteContextInterface)) {
            throw new \RuntimeException('Invalid context');
        }

        $specifications = $this->get('ynlo.tagged_services')->findTaggedServices('ynlo.form.autocomplete.provider');
        foreach ($specifications as $specification) {
            /** @var AutocompleteProviderInterface $service */
            $service = $specification->getService();
            if ($service->getName() === $context->getProvider()) {
                $results = $service->fetchResults($request, $context);

                return $service->buildResponse($results, $context);
            }
        }

        throw new \LogicException('Provider for this context not found');
    }
}
