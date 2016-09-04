<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader;

interface ReaderInterface extends \Iterator, \SeekableIterator, \Countable, \Serializable
{
    /**
     * Select the columns to read for each line.
     *
     * @param array $columns Array of index. (e.g. [1, 5, 23])
     *
     * @return ReaderInterface
     */
    public function selectColumns(array $columns);

    /**
     * Get selected columns.
     *
     * @return array of index.
     */
    public function getSelectedColumns();

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Get file path.
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Get file size.
     *
     * @return int
     */
    public function getFileSize();
}
