<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Admin;

use Rafrsr\LibArray2Object\Object2ArrayBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAbstractAdmin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use YnloFramework\YnloAdminBundle\Action\ActionMapper;
use YnloFramework\YnloAdminBundle\Action\BatchActionMapper;
use YnloFramework\YnloModalBundle\Modal\Modal;
use YnloFramework\YnloModalBundle\Modal\ModalButton;

/**
 * AbstractAdmin.
 */
class AbstractAdmin extends BaseAbstractAdmin
{
    /**
     * @var string
     */
    protected $icon;

    /**
     * Array of actions
     * to show inside a modal.
     *
     * @var array
     */
    protected $onModal
        = [
            'delete',
        ];

    /**
     * Mark a child admin as embedded.
     *
     * @var bool
     */
    protected $embedded;

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * isActionOnModal.
     *
     * @param string $action
     *
     * @return bool
     */
    public function isActionOnModal($action)
    {
        if ($this->isEmbedded() && in_array($action, ['create', 'edit', 'delete'])) {
            return true;
        }

        return in_array($action, $this->onModal, true);
    }

    /**
     * Override this method to customize the modal.
     *
     * @param string $action
     * @param Modal  $modal
     */
    public function configureModal($action, Modal $modal)
    {
        $modal->createButton('cancel', 'Cancel', ModalButton::ACTION_CLOSE);
        $modal->createButton('ok', 'OK', ModalButton::ACTION_SUBMIT);

        if ('create' === $action) {
            $modal->getButton('ok')
                ->setLabel($this->trans('btn_create', [], 'SonataAdminBundle'))
                ->setClass('btn btn-success')
                ->setIcon('fa fa-plus-circle');
        }

        if ('edit' === $action) {
            $modal->getButton('ok')
                ->setLabel($this->trans('btn_update', [], 'SonataAdminBundle'))
                ->setClass('btn btn-primary')
                ->setIcon('fa fa-save');
        }

        if ('delete' === $action) {
            $modal->setTypeDanger()->setTitle($this->trans('title_delete', [], 'SonataAdminBundle'));
            $modal->getButton('ok')
                ->setLabel($this->trans('btn_delete', [], 'SonataAdminBundle'))
                ->setClass('btn btn-danger')
                ->setIcon('fa fa-trash');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateObjectUrl($name, $object, array $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($this->isEmbedded()) {
            $parameters['embedded'] = true;
        }

        return parent::generateObjectUrl($name, $object, $parameters, $absolute);
    }

    /**
     * @return bool
     */
    public function isEmbedded()
    {
        return $this->embedded;
    }

    /**
     * @param bool $embedded
     *
     * @return $this
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmbeddedId()
    {
        return md5($this->code);
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl($name, array $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($this->isEmbedded()) {
            $parameters['embedded'] = true;
        }

        if ($name === 'export') {
            $parameters['data-pjax'] = false;
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }

    /**
     * getSideBarTemplate.
     *
     * @return string
     */
    public function getSideBarTemplate()
    {
        return 'YnloAdminBundle::CRUD/edit_sidebar.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getListToolbarActions()
    {
        $actions = new ActionMapper();
        $this->configureListToolbarActions($actions);

        return $actions;
    }

    /**
     * Configure actions to shown in the list toolbar.
     *
     * @param ActionMapper $actions
     */
    public function configureListToolbarActions(ActionMapper $actions)
    {
        if ($this->hasRoute('create') && $this->isGranted('CREATE')) {
            $actions
                ->add('create', $this->trans('link_action_create', [], 'SonataAdminBundle'))
                ->setIcon('fa fa-plus-circle')
                ->setAttributes(['class' => 'btn btn-success']);
        }
    }

    /**
     * This method return a batch of actions but inside a mapper object
     * is used by YnloAdmin to render better batch actions.
     *
     * @return BatchActionMapper
     */
    public function getBatchActionMapper()
    {
        $actions = $this->getBatchActions();

        $mapper = new BatchActionMapper();
        foreach ($actions as $action => $options) {
            $mapper[$action] = $options;
        }

        return $mapper;
    }

    /**
     * NOTE: use configureBatchActionsUsingMapper to customize batch actions.
     *
     * {@inheritdoc}
     *
     * @internal
     *
     * Override to call configureBatchActionsUsingMapper with mapper instance with current actions
     */
    protected function configureBatchActions($actions)
    {
        $mapper = new BatchActionMapper();
        foreach ($actions as $action => $options) {
            $mapper[$action] = $options;
        }
        $this->configureBatchActionsUsingMapper($mapper);

        //customize delete action
        if ($mapper->has('delete')) {
            $mapper->get('delete')->setIcon('fa fa-trash')->addAttributes(['class' => 'btn btn-danger']);
        }

        //this return a array in the format expected by sonata
        //sonata don`t allow dropdowns, for that reason some child elements
        //appear as duplicated in the array, but note the key visible is false to hide duplicate
        //actions in the template
        return Object2ArrayBuilder::create()->build()->createArray($mapper);
    }

    /**
     * Allows you to customize batch actions.
     * This implementation differ from sonata that this allow use OOP
     * with a ActionMapper and many extra features.
     *
     * @param BatchActionMapper $actions List of actions
     */
    protected function configureBatchActionsUsingMapper(BatchActionMapper $actions)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        if ('list' === $name && $this->isEmbedded()) {
            return 'YnloAdminBundle:CRUD:base_list_embedded.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * Get current action name based on current request.
     *
     * This method can be used to know what is the current action when use
     * configureFormFields to build multiple forms based on action
     *
     * @return string
     */
    public function getCurrentAction()
    {
        return str_replace($this->getBaseRouteName().'_', '', $this->getRequest()->get('_route'));
    }

    /**
     * Check if current action match with given list or string.
     *
     * This method can be used to know what is the current cation when use
     * configureFormFields to build multiple forms based on action
     *
     * @param string|array $actions
     *
     * @return bool
     */
    public function isCurrentAction($actions)
    {
        return in_array($this->getCurrentAction(), (array) $actions);
    }

    /**
     * Check if current action is sonata internal action
     * like retrieve form element when add a option to sonata_type_model.
     *
     * When sonata call some internal actions methods like configureFormFields
     * should return the entire list of fields and not only field for current action
     * otherwise actions like `sonata_admin_retrieve_form_element` may does not work fine
     *
     * @return bool
     */
    public function isInternalAction()
    {
        //TODO: add all internal actions
        return $this->isCurrentAction([
            'sonata_admin_retrieve_form_element',
        ]);
    }
}
