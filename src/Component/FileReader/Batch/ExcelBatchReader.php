<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Batch;

use YnloFramework\Component\FileReader\BatchReaderInterface;
use YnloFramework\Component\FileReader\BatchReaderTrait;
use YnloFramework\Component\FileReader\Reader\ExcelReader;

class ExcelBatchReader extends ExcelReader implements BatchReaderInterface, \PHPExcel_Reader_IReadFilter
{
    use BatchReaderTrait;

    public function __construct($filename, $batchLength = 500, $batchStep = 1, $activeSheet = null)
    {
        $this->step = $batchStep;
        $this->length = max(0, $batchLength);

        parent::__construct($filename, $activeSheet);
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        if (!$this->worksheet) {
            return;
        }

        // normalize index (start with 1)
        ++$position;

        $rowStart = $this->length * ($this->step - 1) + 1;
        $rowEnd = min($this->length * $this->step, $this->count);

        if ($position > $rowEnd) {
            $position = $rowEnd;
        } elseif ($position < $rowStart) {
            $position = $rowStart;
        }

        $this->worksheet->seek($position);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if ($this->length > 0 && $this->key() === $this->length * $this->step) {
            return false;
        }

        return parent::valid();
    }

    /**
     * {@inheritdoc}
     */
    public function advance($step = 1)
    {
        if (!$this->worksheet) {
            return false;
        }

        $step = (int) $step;
        if ($this->step + $step < $this->step) {
            throw new \LogicException('You can\'t regress the batch progress.');
        }

        $this->step += $step;

        return $this->load($this->getFilePath(), $this->activeSheet);
    }

    /**
     * {@inheritdoc}
     */
    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row >= $this->length * ($this->step - 1) && $row <= $this->length * $this->step && $row <= $this->count) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            'filename' => $this->file ? $this->file->getRealPath() : null,
            'headerRowNumber' => $this->headerRowNumber,
            'activeSheet' => $this->activeSheet,
            'columns' => $this->columns,
            'step' => $this->step,
            'length' => $this->length,
            'count' => $this->count,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->headerRowNumber = $data['headerRowNumber'];
        $this->columns = $data['columns'];
        $this->step = $data['step'];
        $this->length = $data['length'];
        $this->count = $data['count'];
        if ($this->load($data['filename'], $data['activeSheet'])) {
            $this->rewind();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function load($filename, $activeSheet)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $this->file = new \SplFileObject($filename);
        $this->worksheet = null;

        /** @var \PHPExcel_Reader_Excel2007 $reader */
        $reader = \PHPExcel_IOFactory::createReaderForFile($this->getFilePath());
        $reader->setReadDataOnly(true);
        $reader->setReadFilter($this);

        if (null === $this->count) {
            // fast count
            $spreadsheetInfo = $reader->listWorksheetInfo($filename);
            $this->count = $spreadsheetInfo[0]['totalRows'];
        }

        $this->length = min($this->length, $this->count);

        if ($this->length * ($this->step - 1) >= $this->count) {
            return false;
        }

        /** @var \PHPExcel $excel */
        $excel = $reader->load($this->file->getPathname());

        if (null !== $activeSheet) {
            $this->activeSheet = $activeSheet;
            $excel->setActiveSheetIndex($activeSheet);
        }

        $this->worksheet = $excel->getActiveSheet()->getRowIterator();
        $this->worksheet->resetStart($this->length * ($this->step - 1) + 1);
        $this->worksheet->resetEnd(min($this->length * $this->step, $this->count));

        return $this->valid();
    }
}
