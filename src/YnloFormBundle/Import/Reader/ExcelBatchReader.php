<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Reader;

class ExcelBatchReader extends ExcelReader implements BatchReaderInterface
{
    use BatchReaderTrait;

    public function __construct($filename, $batchLength = 500, $activeSheet = null)
    {
        parent::__construct($filename, $activeSheet);

        $this->setBatchLength($batchLength);
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
        if (!$this->worksheet || !$this->worksheet->valid()) {
            return false;
        }

        $step = (int) $step;
        if ($this->step + $step < $this->step) {
            throw new \LogicException('You can\'t regress the batch progress.');
        }

        $this->step += $step;
        if ($this->key() !== $this->length * ($this->step - 1)) {
            $this->rewind();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->count && $this->worksheet) {
            $length = $this->length;
            $this->length = 0;
            $this->count = parent::count();
            $this->length = $length;
        }

        return $this->count ?: 0;
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
        ]);
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
        $this->step = $data['step'];
        $this->length = $data['length'];
        $this->rewind();
    }
}
