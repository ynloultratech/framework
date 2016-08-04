<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Action;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Traversable;

/**
 * ActionMapper
 */
class ActionMapper implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array|ActionInterface[]
     */
    protected $actions = [];

    /**
     * Add visual divider.
     *
     * @return $this
     */
    public function addDivider()
    {
        $action = $this->add('divider_'.mt_rand());
        $action->setType(ActionInterface::TYPE_DIVIDER);

        return $this;
    }

    /**
     * Add action.
     *
     * @param string      $action
     * @param string|null $label
     *
     * @return ActionInterface
     */
    public function add($action, $label = null)
    {
        if (!$action instanceof ActionInterface) {
            $action = new Action($action, $label);
        }

        $this->actions[$action->getName()] = $action;

        return $action;
    }

    /**
     * Add separator (NON visual).
     *
     * @return $this
     */
    public function addSeparator()
    {
        $action = $this->add('separator_'.mt_rand());
        $action->setType(ActionInterface::TYPE_SEPARATOR);

        return $this;
    }

    /**
     * Get instance of given action name.
     *
     * @param string $action
     *
     * @return ActionInterface
     */
    public function get($action)
    {
        if (!isset($this->actions[$action])) {
            throw new \LogicException(sprintf('The action "%s" not exists.', $action));
        }

        return $this->actions[$action];
    }

    /**
     * get all actions.
     *
     * @return ActionInterface[]
     */
    public function getAll()
    {
        return $this->actions ?: [];
    }

    /**
     * Has action.
     *
     * @param string $action
     *
     * @return bool
     */
    public function has($action)
    {
        return isset($this->actions[$action]);
    }

    /**
     * Remove action.
     *
     * @param string $action
     *
     * @return $this
     */
    public function remove($action)
    {
        unset($this->actions[$action]);

        return $this;
    }

    /**
     * Is empty actions.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->actions) === 0;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->actions[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $action = new Action($offset);
        $accessor = new PropertyAccessor();
        foreach ($value as $property => $val) {
            $accessor->setValue($action, $property, $val);
        }
        $this->actions[$offset] = $action;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->actions[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->actions);
    }
}
