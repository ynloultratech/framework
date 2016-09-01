<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Reader;

class ExcelReader implements ReaderInterface
{
    use ReaderTrait;

    /**
     * @var \PHPExcel_Worksheet_RowIterator
     */
    protected $worksheet;

    /**
     * @var string
     */
    protected $activeSheet;

    /**
     * @var int
     */
    protected $count;

    /**
     * Excel reader Constructor.
     *
     * @param string $filename    Excel filename
     * @param int    $activeSheet Index of active sheet to read from
     */
    public function __construct($filename, $activeSheet = null)
    {
        $this->load($filename, $activeSheet);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (!$this->worksheet) {
            return;
        }

        $row = [];
        /* @var \PHPExcel_Cell $cell */
        foreach ($this->worksheet->current()->getCellIterator() as $cell) {
            $row[] = $cell->getValue();
        }

        if (!empty($this->columns)) {
            $keysCount = count($keys = array_values($this->columns));
            $valuesCount = count($values = array_intersect_key($row, $this->columns));

            if ($keysCount > $valuesCount) {
                // Line too short
                $values = array_pad($values, $keysCount, null);
            } elseif ($keysCount < $valuesCount) {
                // Line too long
                $values = array_slice($values, 0, $keysCount);
            }

            return array_combine($keys, $values);
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if ($this->worksheet) {
            $this->worksheet->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->worksheet ? $this->worksheet->key() - 1 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->worksheet ? $this->worksheet->valid() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->worksheet) {
            if (null === $this->headerRowNumber) {
                $this->worksheet->rewind();
            } else {
                $this->worksheet->seek($this->headerRowNumber + 1);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            [
                'filename' => $this->file ? $this->file->getRealPath() : null,
                'headerRowNumber' => $this->headerRowNumber,
                'activeSheet' => $this->activeSheet,
                'columns' => $this->columns,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->load($data['filename'], $data['activeSheet']);
        $this->headerRowNumber = $data['headerRowNumber'];
        $this->columns = $data['columns'];
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->count && $this->worksheet) {
            $position = $this->key();
            $this->count = iterator_count($this->worksheet);
            $this->seek($position);
        }

        $count = $this->count ? $this->count - ((int) $this->headerRowNumber) : 0;

        return $count >= 0 ? $count : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        if ($this->worksheet) {
            $this->worksheet->seek($position + 1);
        }
    }

    protected function load($filename, $activeSheet)
    {
        if (!file_exists($filename)) {
            return;
        }

        $this->file = new \SplFileObject($filename);

        $reader = \PHPExcel_IOFactory::createReaderForFile($this->getFilePath());
        /** @var \PHPExcel $excel */
        $excel = $reader->load($this->file->getPathname());

        if (null !== $activeSheet) {
            $this->activeSheet = $activeSheet;
            $excel->setActiveSheetIndex($activeSheet);
        }

        $this->worksheet = $excel->getActiveSheet()->getRowIterator();
    }
}
