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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\HttpFoundation\JsonResponse;

class Select2Provider extends SimpleEntityProvider implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'select2';
    }

    /**
     * @inheritDoc
     */
    public function buildResponse(AutocompleteResults $results, AutocompleteContextInterface $context)
    {
        $array = [];
        $templateResult = $context->getParameter('select2_template_result');
        $templateSelection = $context->getParameter('select2_template_selection');

        foreach ($results as $id => $result) {
            $array[] = [
                'id' => $id,
                'text' => $this->renderSelect2Item($id, $result, $templateResult),
                'selection_text' => $this->renderSelect2Item($id, $result, $templateSelection),
            ];
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

    /**
     * renderSelect2Item
     *
     * @param      $id
     * @param      $result
     * @param null $template
     *
     * @return string
     */
    protected function renderSelect2Item($id, $result, $template = null)
    {
        if ($template) {
            $choice = new ChoiceView($id, (string)$result, (string)$result);
            if (is_string($template)) {
                $text = $this->container->get('templating')->render(
                    $template,
                    [
                        'choice' => $choice,
                        'object' => $result
                    ]
                );
            } else {
                $text = call_user_func_array($template, [$choice, $result]);
            }
        } else {
            $text = (string)$result;
        }

        return $text;
    }
}