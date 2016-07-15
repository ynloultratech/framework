<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloModalBundle\Modal;

use Rafrsr\LibArray2Object\Object2ArrayInterface;

class ModalButton implements Object2ArrayInterface
{
    const ACTION_CLOSE = 'close';
    const ACTION_SUBMIT = 'submit';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var bool
     */
    protected $autospin = false;

    /**
     * @param string $id
     * @param string $label
     * @param string $icon
     * @param string $class
     * @param string $action
     */
    public function __construct($id, $label, $action = null, $class = null, $icon = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->icon = $icon;
        $this->class = $class;
        $this->action = $action;

        if ($action === self::ACTION_SUBMIT) {
            $this->setAutospin(true);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return $this
     */
    public function setActionClose()
    {
        return $this->setAction(self::ACTION_CLOSE);
    }

    /**
     * @return $this
     */
    public function setActionSubmit()
    {
        return $this->setAction(self::ACTION_SUBMIT);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

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
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutospin()
    {
        return $this->autospin;
    }

    /**
     * @param boolean $autospin
     *
     * @return $this
     */
    public function setAutospin($autospin)
    {
        $this->autospin = $autospin;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toArray()
    {
        return [
            'id' => $this->getId(),
            'label' => $this->getLabel(),
            'action' => $this->getAction(),
            'cssClass' => $this->getClass(),
            'icon' => $this->getIcon(),
            'autospin' => $this->isAutospin(),
        ];
    }
}