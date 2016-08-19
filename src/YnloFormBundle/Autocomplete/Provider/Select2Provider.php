<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete\Provider;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\HttpFoundation\JsonResponse;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteContextInterface;
use YnloFramework\YnloFormBundle\Autocomplete\AutocompleteResults;

class Select2Provider extends SimpleEntityProvider implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'select2';
    }

    /**
     * {@inheritdoc}
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
                ],
            ]
        );
    }

    /**
     * renderSelect2Item.
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
            $choice = new ChoiceView($id, (string) $result, (string) $result);
            if (is_string($template)) {
                $text = $this->container->get('templating')->render(
                    $template,
                    [
                        'choice' => $choice,
                        'object' => $result,
                    ]
                );
            } else {
                $text = call_user_func_array($template, [$choice, $result]);
            }
        } else {
            $text = (string) $result;
        }

        return $text;
    }
}
