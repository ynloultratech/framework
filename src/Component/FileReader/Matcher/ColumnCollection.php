<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Matcher;

use Doctrine\Common\Collections\ArrayCollection;
use YnloFramework\Component\FileReader\Exception\UnexpectedTypeException;

class ColumnCollection extends ArrayCollection
{
    /**
     * @param Column $column
     *
     * @return bool
     */
    public function removeElement($column)
    {
        $this->columnTypeExpected($column);

        return parent::removeElement($column);
    }

    /**
     * @param mixed  $offset
     * @param Column $column
     *
     * @return bool|void
     */
    public function offsetSet($offset, $column)
    {
        $this->columnTypeExpected($column);

        return parent::offsetSet($offset, $column);
    }

    /**
     * @param Column $column
     *
     * @return bool
     */
    public function contains($column)
    {
        $this->columnTypeExpected($column);

        return parent::contains($column);
    }

    /**
     * @param Column $column
     *
     * @return bool|int|string
     */
    public function indexOf($column)
    {
        $this->columnTypeExpected($column);

        return parent::indexOf($column);
    }

    /**
     * @param Column $column
     *
     * @return bool
     */
    public function add($column)
    {
        $this->columnTypeExpected($column);

        return parent::add($column);
    }

    /**
     * @param int|string $key
     * @param Column     $column
     */
    public function set($key, $column)
    {
        $this->columnTypeExpected($column);

        parent::set($key, $column);
    }

    /**
     * @param ColumnCollection $collection
     */
    public function merge(ColumnCollection $collection)
    {
        foreach ($collection as $newColumn) {
            $exists = $this->exists(function ($name, Column $column) use ($newColumn) {
                return $column->getIndex() === $newColumn->getIndex();
            });

            if (!$exists) {
                $this->set($newColumn->getName(), $newColumn);
            }
        }
    }

    private function columnTypeExpected($value)
    {
        if (!$value instanceof Column) {
            throw new UnexpectedTypeException($value, Column::class);
        }
    }
}
