<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Matcher;

use YnloFramework\YnloFormBundle\Import\Reader\BatchReaderInterface;

class ColumnMatcher
{
    /**
     * Columns to select.
     *
     * @var ColumnCollection|Column[]
     */
    private $columns;

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
     * @return BatchReaderInterface
     */
    public function getPreviewData($size = 10)
    {
        $this->reader->setBatchLength($size);

        return $this->reader;
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

        return;
    }
}
