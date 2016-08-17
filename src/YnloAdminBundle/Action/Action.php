<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Action;

/**
 * Class Action.
 */
class Action implements ActionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParameters = [];

    /**
     * @var array
     */
    protected $attributes
        = [
            'class' => 'btn btn-default',
        ];

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @var ActionMapper
     */
    protected $dropDown;

    /**
     * Settings custom template.
     *
     * @var string
     */
    protected $template;

    /**
     * Action type.
     *
     * @var string
     */
    protected $type = ActionInterface::TYPE_BTN;

    /**
     * @var bool
     */
    protected $visible = true;

    /**
     * Action constructor.
     *
     * @param string      $name
     * @param string|null $label
     */
    public function __construct($name, $label = null)
    {
        $this->name = $name;
        $this->label = $label ?: $name;
        $this->dropDown = new ActionMapper();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDropDown()
    {
        return $this->dropDown;
    }

    /**
     * @param ActionMapper|array $dropDown
     *
     * @return $this
     */
    public function setDropDown($dropDown)
    {
        if (is_array($dropDown)) {
            $childActions = new ActionMapper();
            foreach ($dropDown as $action => $config) {
                $childActions[$action] = $config;
            }
            $dropDown = $childActions;
        }
        $this->dropDown = $dropDown;

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
     * {@inheritdoc}
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * addAttributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $this->template = $template;

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
     * @return Action
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Is divider.
     *
     * @return bool
     */
    public function isDivider()
    {
        return $this->type === ActionInterface::TYPE_DIVIDER;
    }

    /**
     * Is separator.
     *
     * @return bool
     */
    public function isSeparator()
    {
        return $this->type === ActionInterface::TYPE_SEPARATOR;
    }

    /**
     * Is button.
     *
     * @return bool
     */
    public function isButton()
    {
        return $this->type === ActionInterface::TYPE_BTN;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }
}
