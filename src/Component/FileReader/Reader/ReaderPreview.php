<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader\Reader;

use YnloFramework\Component\FileReader\ReaderInterface;

class ReaderPreview implements \Iterator, \Countable, \SeekableIterator
{
    private $reader;
    private $size;
    private $read = 0;
    private $count;

    public function __construct(ReaderInterface $reader, int $size)
    {
        $this->reader = clone $reader;
        $this->size = $size - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->reader->current();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->reader->next();
        ++$this->read;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->reader->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->read <= $this->size && $this->reader->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->read = 0;
        $this->reader->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        if (null === $this->count) {
            $position = $this->key();
            $this->count = iterator_count($this);
            $this->seek($position);
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        $this->reader->seek($position);
    }

    /**
     * Get reader.
     *
     * @return ReaderInterface
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Get preview size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size + 1;
    }
}
