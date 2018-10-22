<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Matcher;

use YnloFramework\Component\FileReader\BatchReaderInterface;

class ColumnMatcher
{
    /**
     * Columns to select.
     *
     * @var ColumnCollection|Column[]
     */
    private $columns;

    /**
     * @var array
     */
    private $restrictedColumns = [];

    /**
     * @var BatchReaderInterface
     */
    private $reader;

    /**
     * ColumnMatcher constructor.
     *
     * @param BatchReaderInterface $reader
     */
    public function __construct(BatchReaderInterface $reader)
    {
        $this->columns = new ColumnCollection();
        $this->reader = $reader;
    }

    /**
     * Get columns.
     *
     * @return ColumnCollection|Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get restricted columns.
     *
     * @return array
     */
    public function getRestrictedColumns()
    {
        return $this->restrictedColumns ?: [];
    }

    /**
     * Set restricted columns.
     *
     * @param array $columns
     *
     * @return self
     */
    public function setRestrictedColumns($columns)
    {
        $this->restrictedColumns = $columns;

        return $this;
    }

    /**
     * Add restricted column.
     *
     * @param int $index
     *
     * @return self
     */
    public function addRestrictedColumn($index)
    {
        $this->restrictedColumns[$index] = true;

        return $this;
    }

    /**
     * Is restricted column.
     *
     * @param int $index
     *
     * @return bool
     */
    public function isRestrictedColumn($index)
    {
        return isset($this->restrictedColumns[$index]);
    }

    /**
     * Get preview columns.
     *
     * @return array
     */
    public function getPreviewColumns()
    {
        if (!$this->reader->valid()) {
            $this->reader->rewind();
        }

        return $this->reader->current();
    }

    /**
     * Get preview data.
     *
     * @param int $size
     *
     * @return \Generator
     */
    public function getPreviewData($size = 10)
    {
        $count = 0;
        foreach ($this->reader as $row) {
            if (++$count > $size) {
                break;
            }

            yield $row;
        }
    }

    /**
     * @return BatchReaderInterface
     */
    public function getReader()
    {
        return $this->reader;
    }

    public function updateReaderColumns()
    {
        $this->reader->selectColumns($this->getResult());
    }

    public function clearReaderColumns()
    {
        $this->reader->selectColumns([]);
    }

    public function getPreselectedData($index)
    {
        foreach ($this->columns as $column) {
            if ($column->getIndex() === $index) {
                return $column->getName();
            }
        }
    }

    public function getSimilarData(string $name, array &$history): ?string
    {
        $list = [];
        foreach ($this->columns as $column) {
            $lev = levenshtein($name, $column->getLabel());
            if ($lev < 4 || $lev <= \strlen($name) / 2 || false !== strpos($column->getLabel(), $name)) {
                $list[$column->getName()] = $lev;
            }
        }
        uasort($list, function ($a, $b) { return $a <=> $b; });

        foreach (array_keys($list) as $key) {
            if (!isset($history[$key])) {
                $history[$key] = true;

                return $key;
            }
        }

        return null;
    }

    /**
     * Get column.
     *
     * @param int $index
     *
     * @return Column
     */
    public function getColumn($index)
    {
        foreach ($this->columns as $column) {
            if ($column->getIndex() === $index) {
                return $column;
            }
        }
    }

    /**
     * Get matched index columns.
     *
     * @return array of index
     */
    public function getResult()
    {
        $result = [];

        foreach ($this->columns as $column) {
            $result[$column->getName()] = $column->getIndex();
        }

        return $result;
    }
}
