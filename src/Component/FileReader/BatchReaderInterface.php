<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader;

interface BatchReaderInterface extends ReaderInterface
{
    /**
     * Get batch length.
     *
     * @return int
     */
    public function getBatchLength();

    /**
     * Gets current batch step.
     *
     * @return int
     */
    public function getBatchStep();

    /**
     * Checks if current step is valid and advance the batch X steps.
     *
     * @param int $step
     *
     * @return bool
     */
    public function advance($step = 1);
}
