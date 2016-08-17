<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Action;

use Rafrsr\LibArray2Object\Object2ArrayBuilder;
use Rafrsr\LibArray2Object\Object2ArrayInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class BatchActionMapper.
 */
class BatchActionMapper extends ActionMapper implements Object2ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function add($action, $label = null)
    {
        if (!$action instanceof ActionInterface) {
            $action = new BatchAction($action, $label);
        }

        $this->actions[$action->getName()] = $action;

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $action = new BatchAction($offset);
        $accessor = new PropertyAccessor();
        foreach ($value as $property => $val) {
            $accessor->setValue($action, $property, $val);
        }
        $this->actions[$offset] = $action;
    }

    /**
     * Get array in sonata format.
     *
     * @return array
     */
    public function __toArray()
    {
        $batchActionsArray = [];
        foreach ($this->getAll() as $action) {
            $batchActionsArray[$action->getName()] = $action;

            //SonataAdmin require all actions in the root
            //mark each shild is a way to hide in the template
            if (!$action->getDropDown()->isEmpty()) {
                $childArray = $action->getDropDown();

                foreach ($childArray as $childAction => $childSettings) {
                    $childSettings = Object2ArrayBuilder::create()->build()->createArray($childSettings);
                    $childSettings['visible'] = false;
                    $batchActionsArray[$childAction] = $childSettings;
                }
            }
        }

        return $batchActionsArray;
    }
}
