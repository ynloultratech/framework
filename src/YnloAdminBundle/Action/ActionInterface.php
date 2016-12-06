<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Action;

interface ActionInterface
{
    const TYPE_BTN = 'btn';
    const TYPE_DIVIDER = '|';
    const TYPE_SEPARATOR = '_';

    /**
     * @return string
     */
    public function getName();

    /**
     * Name of the action.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * Label to show to users.
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get child mapper instance to add sub actions and create a dropdown.
     *
     * @return ActionMapper
     */
    public function getDropDown();

    /**
     * @return string
     */
    public function getIcon();

    /**
     * Add custom icon, an <i> element will be created with the $icon as class.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon);

    /**
     * setAttribute.
     *
     * @param array $attrs array of attribues
     *
     * @return $this
     */
    public function setAttributes(array $attrs);

    /**
     * Merge current attributes with given.
     *
     * @param array $attrs
     *
     * @return mixed
     */
    public function addAttributes(array $attrs);

    /**
     * setAttribute.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Name of the route to use.
     *
     * @return string
     */
    public function getRoute();

    /**
     * Set the route to use to launch this action.
     *
     * @param string $route
     *
     * @return $this
     */
    public function setRoute($route);

    /**
     * Array of route params.
     *
     * @return array
     */
    public function getRouteParameters();

    /**
     * Set array of route params.
     *
     * @param array $routeParams
     *
     * @return $this
     */
    public function setRouteParameters(array $routeParams);

    /**
     * @return string
     */
    public function getTranslationDomain();

    /**
     * @param string $translationDomain
     *
     * @return $this
     */
    public function setTranslationDomain($translationDomain);

    /**
     * Set template.
     *
     * @param string $template
     *
     * @return ActionMapper
     */
    public function setTemplate($template);

    /**
     * Get template.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return ActionMapper
     */
    public function setType($type);

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();
}
