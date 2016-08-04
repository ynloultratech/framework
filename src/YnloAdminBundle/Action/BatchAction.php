<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Action;

use Rafrsr\LibArray2Object\Object2ArrayInterface;

/**
 * Class BatchAction
 *
 * @method BatchActionMapper getDropDown()
 */
class BatchAction extends Action implements Object2ArrayInterface
{
    /**
     * @var bool
     */
    protected $askConfirmation;

    /**
     * @param ActionMapper|array $dropDown
     *
     * @return $this
     */
    public function setDropDown($dropDown)
    {
        if (is_array($dropDown)) {
            $childActions = new BatchActionMapper();
            foreach ($dropDown as $action => $config) {
                $childActions[$action] = $config;
            }
            $dropDown = $childActions;
        }
        $this->dropDown = $dropDown;

        return $this;
    }

    /**
     * toArray.
     *
     * @return array
     */
    public function __toArray()
    {
        $childActions = [];
        foreach ($this->getDropDown()->getAll() as $action => $config) {
            $childActions[$action] = $config;
        }

        return [
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'type' => $this->getType(),
            'visible' => $this->isVisible(),
            'attributes' => $this->getAttributes(),
            'ask_confirmation' => $this->isAskConfirmation(),
            'translation_domain' => $this->getTranslationDomain(),
            'dropDown' => $childActions,
        ];
    }

    /**
     * @return bool
     */
    public function isAskConfirmation()
    {
        return $this->askConfirmation;
    }

    /**
     * Request for comfirmation before action, default: true.
     *
     * @param bool $askConfirmation
     *
     * @return $this
     */
    public function setAskConfirmation($askConfirmation)
    {
        $this->askConfirmation = $askConfirmation;

        return $this;
    }
}
