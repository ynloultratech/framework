<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Reader;

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
    public function setBatchLength($length)
    {
        $length = (int) $length;
        if ($length < 0) {
            throw new \InvalidArgumentException('The batch length must be an integer positive.');
        }

        $this->length = min($length, $this->count());
        $this->rewind();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBatchLength()
    {
        return $this->length;
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
