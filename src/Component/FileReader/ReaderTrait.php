<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader;

trait ReaderTrait
{
    /**
     * @var \SplFileObject
     */
    protected $file;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $headerRowNumber;

    /**
     * @var array
     */
    protected $columns;

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return $this->file ? $this->file->getFilename() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePath()
    {
        return $this->file ? $this->file->getPathname() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileSize()
    {
        return $this->file ? $this->file->getSize() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function selectColumns(array $columns)
    {
        $this->columns = array_flip(array_filter($columns, function ($value) {
            return null !== $value;
        }));
        ksort($this->columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectedColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $headerRowNumber
     *
     * @return ExcelReader
     */
    public function setHeaderRowNumber($headerRowNumber)
    {
        $this->headerRowNumber = $headerRowNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeaderRowNumber()
    {
        return $this->headerRowNumber;
    }
}
