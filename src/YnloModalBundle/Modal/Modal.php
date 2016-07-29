<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\Modal;

use Rafrsr\LibArray2Object\Object2ArrayInterface;

class Modal implements Object2ArrayInterface
{
    const TYPE_DEFAULT = 'type-default';
    const TYPE_INFO = 'type-info';
    const TYPE_PRIMARY = 'type-primary';
    const TYPE_SUCCESS = 'type-success';
    const TYPE_WARNING = 'type-warning';
    const TYPE_DANGER = 'type-danger';

    const SIZE_NORMAL = 'size-normal';
    const SIZE_SMALL = 'size-small';
    const SIZE_WIDE = 'size-wide';
    const SIZE_LARGE = 'size-large';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $type = self::TYPE_DEFAULT;

    /**
     * @var
     */
    protected $cssClass;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array|ModalButton[]
     */
    protected $buttons = [];

    /**
     * @var bool
     */
    protected $closable = true;

    /**
     * @var bool
     */
    protected $closeByBackdrop = false;

    /**
     * @var bool
     */
    protected $closeByKeyboard = true;

    /**
     * @var string
     */
    protected $spinicon = 'fa fa-spinner fa-pulse';

    /**
     * @var bool
     */
    protected $animate = true;

    /**
     * Modal constructor.
     *
     * @param string $message
     * @param string $title
     * @param string $icon
     */
    public function __construct($message = null, $title = null, $icon = null)
    {
        $this->message = $message;
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeNormal()
    {
        $this->setSize(self::SIZE_NORMAL);

        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeSmall()
    {
        $this->setSize(self::SIZE_SMALL);

        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeLarge()
    {
        $this->setSize(self::SIZE_LARGE);

        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeWide()
    {
        $this->setSize(self::SIZE_WIDE);

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
     * @return mixed
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param mixed $cssClass
     *
     * @return $this
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpinicon()
    {
        return $this->spinicon;
    }

    /**
     * @param string $spinicon
     *
     * @return $this
     */
    public function setSpinicon($spinicon)
    {
        $this->spinicon = $spinicon;

        return $this;
    }

    /**
     * @return array|ModalButton[]
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @return ModalButton
     */
    public function getButton($id)
    {
        return $this->buttons[$id];
    }

    /**
     * @param array|ModalButton[] $buttons
     *
     * @return $this
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $action
     * @param string $class
     * @param string $icon
     *
     * @return ModalButton
     */
    public function createButton($id, $label, $action, $class = null, $icon = null)
    {
        $button = new ModalButton($id, $label, $action, $class, $icon);
        $this->addButton($button);

        return $button;
    }

    /**
     * @param ModalButton $button
     *
     * @return $this
     */
    public function addButton(ModalButton $button)
    {
        $this->buttons[$button->getId()] = $button;

        return $this;
    }

    /**
     * @return bool
     */
    public function isClosable()
    {
        return $this->closable;
    }

    /**
     * @param bool $closable
     *
     * @return $this
     */
    public function setClosable($closable)
    {
        $this->closable = $closable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCloseByBackdrop()
    {
        return $this->closeByBackdrop;
    }

    /**
     * @param bool $closeByBackdrop
     *
     * @return $this
     */
    public function setCloseByBackdrop($closeByBackdrop)
    {
        $this->closeByBackdrop = $closeByBackdrop;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCloseByKeyboard()
    {
        return $this->closeByKeyboard;
    }

    /**
     * @param bool $closeByKeyboard
     *
     * @return $this
     */
    public function setCloseByKeyboard($closeByKeyboard)
    {
        $this->closeByKeyboard = $closeByKeyboard;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAnimate()
    {
        return $this->animate;
    }

    /**
     * @param bool $animate
     *
     * @return $this
     */
    public function setAnimate($animate)
    {
        $this->animate = $animate;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeWarning()
    {
        $this->setType(self::TYPE_WARNING);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeInfo()
    {
        $this->setType(self::TYPE_INFO);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypePrimary()
    {
        $this->setType(self::TYPE_PRIMARY);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDanger()
    {
        $this->setType(self::TYPE_DANGER);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeSuccess()
    {
        $this->setType(self::TYPE_SUCCESS);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDefault()
    {
        $this->setType(self::TYPE_DEFAULT);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toArray()
    {
        return [
            'title' => $this->getTitle(),
            'type' => $this->getType(),
            'size' => $this->getSize(),
            'closable' => $this->isClosable(),
            'animate' => $this->isAnimate(),
            'closeByBackdrop' => $this->isCloseByBackdrop(),
            'closeByKeyboard' => $this->isCloseByKeyboard(),
            'spinicon' => $this->getSpinicon(),
            'cssClass' => $this->getCssClass(),
            'buttons' => $this->getButtons(),
            'message' => $this->getMessage(),
        ];
    }
}
