<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
 */

namespace YnloFramework\Component\FileReader\Tests\Reader;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use YnloFramework\Component\FileReader\Reader\CsvReader;
use YnloFramework\Component\FileReader\Reader\ExcelReader;
use YnloFramework\Component\FileReader\Reader\ReaderPreview;
use YnloFramework\Component\FileReader\ReaderInterface;

class ReaderPreviewTest extends TestCase
{
    /**
     * @var ReaderPreview
     */
    private $preview;

    /**
     * @var ReaderInterface
     */
    private $reader;

    public function setUp()
    {
        $this->reader = new CsvReader(__DIR__.'/../fixtures/data.csv', '|');
        $this->preview = new ReaderPreview($this->reader, 5);
    }

    public function testCounters()
    {
        $this->assertNotSame($this->reader, $this->preview->getReader());
        $this->assertSame(5, $this->preview->getSize());
        $this->assertSame(0, $this->preview->key());
        $this->assertSame(5, count($this->preview));
        $this->assertSame(0, $this->preview->key());
        $this->assertSame(17, count($this->reader));
    }

    public function testCsvPreview()
    {
        $count = 0;
        foreach ($this->preview as $row) {
            ++$count;
        }

        $this->assertSame(5, $count);
        $this->assertSame(5, $this->preview->key());
        $this->assertSame(0, $this->reader->key());
    }

    public function testExcelPreview()
    {
        $reader = new ExcelReader(__DIR__.'/../fixtures/data.xlsx');
        $preview = new ReaderPreview($reader, 5);

        $count = 0;
        foreach ($preview as $row) {
            ++$count;
        }

        $this->assertSame(5, $count);
        $this->assertSame(5, $preview->key());
        $this->assertSame(0, $reader->key());
    }
}
