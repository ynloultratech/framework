<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Builder;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;

class ListActionFieldBuilder implements ListFieldBuilderInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * {@inheritdoc}
     */
    public function supportThisField(FieldDescriptionInterface $fieldDescription)
    {
        return $fieldDescription->getName() === '_action';
    }

    /**
     * {@inheritdoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        if (null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('SonataAdminBundle:CRUD:list__action.html.twig');
        }

        if (null === $fieldDescription->getType()) {
            $fieldDescription->setType('action');
        }

        if (null === $fieldDescription->getOption('name')) {
            $fieldDescription->setOption('name', 'Action');
        }

        if (null === $fieldDescription->getOption('code')) {
            $fieldDescription->setOption('code', 'Action');
        }

        if (null === $fieldDescription->getOption('header_style') && $fieldDescription->getOption('dropdown')) {
            $fieldDescription->setOption('header_style', 'width:40px');
        }

        if (null !== $fieldDescription->getOption('actions')) {
            $actions = $fieldDescription->getOption('actions');
            foreach ($actions as $k => &$action) {
                //only set the template if really exists
                //set to default any template that not exists
                if (!isset($action['template'])) {
                    if ($fieldDescription->getOption('dropdown')) {
                        $template = sprintf('SonataAdminBundle:CRUD:list__action_dropdown_%s.html.twig', $k);
                    } else {
                        $template = sprintf('SonataAdminBundle:CRUD:list__action_%s.html.twig', $k);
                    }

                    try {
                        $this->twig->loadTemplate($template);
                    } catch (\Twig_Error_Loader $e) {
                        if ($fieldDescription->getOption('dropdown')) {
                            $template = 'YnloAdminBundle::CRUD/list__action_dropdown_default.html.twig';
                        } else {
                            $template = 'YnloAdminBundle::CRUD/list__action_default.html.twig';
                        }
                    }
                    $action['template'] = $template;
                }

                //set default role
                if (!isset($action['role'])) {
                    $role = strtoupper($k);
                    $action['role'] = $role === 'SHOW' ? 'VIEW' : $role;
                }

                //set default visibility
                if (!isset($action['visible'])) {
                    $action['visible'] = true;
                }
            }

            $fieldDescription->setOption('actions', $actions);
        }

        //hide default label
        if (in_array($fieldDescription->getOption('label'), ['_action', 'Action'])) {
            $fieldDescription->setOption('label', ' ');
        }

        return $fieldDescription;
    }

    /**
     * @param \Twig_Environment $twig
     *
     * @return $this
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }
}
