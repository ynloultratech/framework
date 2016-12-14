<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader;

trait BatchReaderTrait
{
    use ReaderTrait;

    /**
     * @var int
     */
    protected $step = 1;

    /**
     * @var int
     */
    protected $length = 0;

    /**
     * {@inheritdoc}
     */
    public function getBatchLength()
    {
        return $this->length;
    }

    /**
     * Gets the current step.
     *
     * @return int
     */
    public function getBatchStep()
    {
        return $this->step;
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
    public function rewind()
    {
        if (null !== $this->headerRowNumber && 1 === $this->step) {
            $this->seek($this->headerRowNumber);
        } else {
            $this->seek($this->length * ($this->step - 1));
        }
    }
}
