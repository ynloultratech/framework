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
use Sonata\AdminBundle\Guesser\TypeGuesserInterface;
use Sonata\DoctrineORMAdminBundle\Builder\ListBuilder as BaseListBuilder;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;

class ListBuilder extends BaseListBuilder
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * {@inheritdoc}
     */
    public function __construct(TypeGuesserInterface $guesser, array $templates = [])
    {
        parent::__construct($guesser, isset($templates['types']['list']) ? $templates['types']['list'] : $templates);
    }

    /**
     * {@inheritdoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        if ('_details' === $fieldDescription->getName()) {
            $this->buildDetailsFieldDescription($fieldDescription);
        }

        if ('enum' === $fieldDescription->getType() && null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list_enum.html.twig');

            /** @var ModelManager $modelManager */
            $modelManager = $admin->getModelManager();
            if (null === $fieldDescription->getOption('enum_type') && $modelManager->hasMetadata($admin->getClass())) {
                $mapping = $modelManager->getMetadata($admin->getClass())->getFieldMapping($fieldDescription->getName());
                $fieldDescription->setOption('enum_type', $mapping['type']);
            }
        }

        if ('collection' === $fieldDescription->getType() && null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list_collection.html.twig');
        }

        if ('role_hierarchy' === $fieldDescription->getType() && null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list_role_hierarchy.html.twig');
        }

        parent::fixFieldDescription($admin, $fieldDescription);
    }

    /**
     * Build details field description.
     *
     * @param FieldDescriptionInterface $fieldDescription
     */
    public function buildDetailsFieldDescription(FieldDescriptionInterface $fieldDescription)
    {
        if (null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('YnloAdminBundle::CRUD/list__details.html.twig');
        }

        if (null === $fieldDescription->getType()) {
            $fieldDescription->setType('_details');
        }

        if (null === $fieldDescription->getOption('name')) {
            $fieldDescription->setOption('name', 'Details');
        }

        if (null === $fieldDescription->getOption('header_style')) {
            $fieldDescription->setOption('header_style', 'width:50px');
        }

        if (null === $fieldDescription->getOption('details_template')) {
            throw new \LogicException('The option "details_template" is required');
        }

        //encode the template
        $template = base64_encode($fieldDescription->getOption('details_template'));
        $fieldDescription->setOption('details_template_encoded', $template);

        if (null === $fieldDescription->getOption('ajax')) {
            $fieldDescription->setOption('ajax', false);
        }

        if (null === $fieldDescription->getOption('code')) {
            $fieldDescription->setOption('code', '_details');
        }

        //hide default label
        if ($fieldDescription->getOption('label') == '_details' || $fieldDescription->getOption('label') == 'Details') {
            $fieldDescription->setOption('label', ' ');
        }
    }

    /**
     * @param FieldDescriptionInterface $fieldDescription
     *
     * @return FieldDescriptionInterface
     */
    public function buildActionFieldDescription(FieldDescriptionInterface $fieldDescription)
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
