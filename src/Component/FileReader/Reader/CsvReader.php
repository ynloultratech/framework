<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Reader;

use YnloFramework\Component\FileReader\ReaderInterface;
use YnloFramework\Component\FileReader\ReaderTrait;

class CsvReader implements ReaderInterface
{
    use ReaderTrait;

    public function __construct($filename, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if (file_exists($filename)) {
            $this->load($filename, $delimiter, $enclosure, $escape);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $row = $this->file ? $this->file->current() : null;

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
        if ($this->file) {
            $this->file->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file ? $this->file->key() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file ? $this->file->valid() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->file) {
            if (null === $this->headerRowNumber) {
                $this->file->rewind();
            } else {
                $this->file->seek($this->headerRowNumber);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        if ($this->file) {
            $this->file->seek($position);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->count && $this->file) {
            $position = $this->key();
            $this->count = iterator_count($this);
            $this->seek($position);
        }

        $count = $this->count ? $this->count - ((int) $this->headerRowNumber) : 0;

        return $count >= 0 ? $count : 0;
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
    }

    /**
     * Load csv file.
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     *
     * @return bool
     */
    public function load($filename, $delimiter, $enclosure, $escape = '\\')
    {
        ini_set('auto_detect_line_endings', true);

        if (!file_exists($filename)) {
            return false;
        }

        $this->file = new \SplFileObject($filename, 'r');
        $this->file->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );
        $this->file->setCsvControl($delimiter, $enclosure, $escape);

        $this->rewind();

        return $this->valid();
    }

    public function __clone()
    {
        if ($this->file) {
            $csvControl = $this->file->getCsvControl();
            $this->load($this->getFilePath(), $csvControl[0], $csvControl[1]);
        }
    }
}
