<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Reader;

class CsvBatchReader extends CsvReader implements BatchReaderInterface
{
    use BatchReaderTrait;

    public function __construct($filename, $batchLength = 500, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        parent::__construct($filename, $delimiter, $enclosure, $escape);

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

        return $this->file ? $this->file->valid() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function advance($step = 1)
    {
        if (!$this->file || !$this->file->valid()) {
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
        if (null === $this->count && $this->file) {
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
        return serialize(
            [
                'filename' => $this->file ? $this->file->getRealPath() : null,
                'csvControl' => $this->file ? $this->file->getCsvControl() : null,
                'columns' => $this->columns,
                'step' => $this->step,
                'length' => $this->length,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (!file_exists($data['filename'])) {
            return;
        }
        $this->load($data['filename'], $data['csvControl'][0], $data['csvControl'][1]);
        $this->columns = $data['columns'];
        $this->step = $data['step'];
        $this->length = $data['length'];
        $this->rewind();
    }
}
