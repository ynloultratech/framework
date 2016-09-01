<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Import\Reader;

interface BatchReaderInterface extends ReaderInterface
{
    /**
     * Set batch length.
     *
     * @param int $length
     *
     * @return CsvBatchReader
     */
    public function setBatchLength($length);

    /**
     * Get batch length.
     *
     * @return int
     */
    public function getBatchLength();

    /**
     * Checks if current step is valid and advance the batch X steps.
     *
     * @param int $step
     *
     * @return bool
     */
    public function advance($step = 1);
}
