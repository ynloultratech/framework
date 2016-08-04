<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAbstractAdmin;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * to show inside a modal
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
     * isActionOnModal
     *
     * @param string $action
     *
     * @return bool
     */
    public function isActionOnModal($action)
    {
        return in_array($action, $this->onModal, true);
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
     * Override this method to customize the modal
     *
     * @param string $action
     * @param Modal  $modal
     */
    public function configureModal($action, Modal $modal)
    {
        $modal->createButton('cancel', 'Cancel', ModalButton::ACTION_CLOSE);
        $modal->createButton('ok', 'OK', ModalButton::ACTION_SUBMIT);

        if ($action === 'create') {
            $modal->getButton('ok')
                ->setLabel($this->trans('btn_create', [], 'SonataAdminBundle'))
                ->setClass('btn btn-success')
                ->setIcon('fa fa-plus-circle');
        }

        if ($action === 'edit') {
            $modal->getButton('ok')
                ->setLabel($this->trans('btn_update', [], 'SonataAdminBundle'))
                ->setClass('btn btn-primary')
                ->setIcon('fa fa-save');
        }

        if ($action === 'delete') {
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
}
