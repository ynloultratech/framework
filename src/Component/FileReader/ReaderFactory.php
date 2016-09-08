<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\Component\FileReader;

use YnloFramework\Component\FileReader\Batch\CsvBatchReader;
use YnloFramework\Component\FileReader\Batch\ExcelBatchReader;
use YnloFramework\Component\FileReader\Reader\CsvReader;
use YnloFramework\Component\FileReader\Reader\ExcelReader;

class ReaderFactory
{
    /**
     * Create reader for file.
     *
     * @param string $filename
     * @param string $originalExtension
     * @param string $delimiter
     *
     * @return ReaderInterface|null
     */
    public static function createReaderForFile($filename, $originalExtension, $delimiter = ',')
    {
        switch (self::guessFileType($originalExtension)) {
            case 'Excel2007':
                return new ExcelReader($filename);
                break;
            case 'Csv':
                return new CsvReader($filename, $delimiter);
                break;
        }
    }

    /**
     * Create batch reader for file.
     *
     * @param string $filename
     * @param string $originalExtension
     * @param string $delimiter
     * @param int    $batchLength
     *
     * @return BatchReaderInterface|null
     */
    public static function createBatchReaderForFile($filename, $originalExtension, $delimiter = ',', $batchLength = 500)
    {
        switch (self::guessFileType($originalExtension)) {
            case 'Excel2007':
                return new ExcelBatchReader($filename, $batchLength);
                break;
            case 'Csv':
                return new CsvBatchReader($filename, $batchLength, $delimiter);
                break;
        }
    }

    private static function guessFileType($originalExtension)
    {
        switch (strtolower($originalExtension)) {
            case 'xlsx': //	Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm': //	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx': //	Excel (OfficeOpenXML) Template
            case 'xltm': //	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                return 'Excel2007';
                break;
            case 'csv':
                return 'Csv';
                break;
            default:
                break;
        }
    }
}
