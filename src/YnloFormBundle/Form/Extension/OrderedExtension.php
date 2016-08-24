<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderedExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'position' => null,
        ]);
        $resolver->setAllowedTypes('position', ['null', 'array']);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $pos = $options['position'];
        if (is_array($pos)) {
            //accept ['before','fieldName']
            if (count($pos) > 1) {
                $pos = [$pos[0] => $pos[1]];
            }
            $view->vars['position'] = $pos;
        }

        $children = $view->children;

        $priorities = [];

        $priority = 0;
        if ($children) {
            //prepare
            foreach ($children as $name => $formView) {
                $priority = $priority + 1000;

                $priorities[$name] = [
                    'priority' => $priority,
                    'form' => $formView,
                    'name' => $name,
                ];

                if (isset($formView->vars['position'])) {
                    $position = array_keys($formView->vars['position'])[0];
                    $relativeField = current($formView->vars['position']);

                    if (!isset($children[$relativeField])) {
                        $msg = sprintf('Cannot put a field %s non existent field "%s".', $position, $relativeField);
                        throw  new \LogicException($msg);
                    }

                    if ($name == $relativeField) {
                        $msg = sprintf('Cannot put a field %s itself.', $position);
                        throw  new \LogicException($msg);
                    }

                    $priorities[$name]['position'] = $position;
                    $priorities[$name]['relative_field'] = $relativeField;
                }
            }

            foreach ($priorities as $name => $settings) {
                if (isset($settings['position'])) {
                    //FIXME: does not work as expected in all situations,
                    // should be fixed to order in the correct way
                    $parentPriority = $priorities[$settings['relative_field']]['priority'];
                    $newPriority = ($settings['position'] == 'before') ? $parentPriority - 1 : $parentPriority + 1;
                    $this->changePriority($priorities, $name, $newPriority);
                }
            }
        }

        $sortFunction = function ($a, $b) {
            if ($a['priority'] == $b['priority']) {
                return 0;
            }

            return $a['priority'] < $b['priority'] ? -1 : 1;
        };
        usort($priorities, $sortFunction);

        $view->children = array_column($priorities, 'form', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    /**
     * changePriority.
     *
     * @param $priorities
     * @param $name
     * @param $priority
     */
    private function changePriority(&$priorities, $name, $priority)
    {
        $priorities[$name]['priority'] = $priority;
        if (isset($priorities[$name]['position'])) {
            $newParentPriority = ($priorities[$name]['position'] == 'before') ? $priority + 1 : $priority - 1;
            $this->changePriority($priorities, $priorities[$name]['relative_field'], $newParentPriority);
        }
    }
}
