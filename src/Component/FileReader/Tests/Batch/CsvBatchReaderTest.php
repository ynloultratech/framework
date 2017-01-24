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

namespace YnloFramework\Component\FileReader\Tests\Batch;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use YnloFramework\Component\FileReader\Batch\CsvBatchReader;

class CsvBatchReaderTest extends TestCase
{
    public function testValid()
    {
        $batch = new CsvBatchReader(__DIR__.'/../fixtures/data.csv', 5, 1, '|');
        $this->assertTrue($batch->valid());
        $this->assertCount(17, $batch);

        return $batch;
    }

    public function testTruncateSeek()
    {
        $batch = new CsvBatchReader(__DIR__.'/../fixtures/data.csv', 5, 1, '|'); //0-4
        $this->assertEquals(0, $batch->key());
        $batch->seek(17);
        $this->assertEquals(16, $batch->key());
    }

    public function testInvalid()
    {
        $batch = new CsvBatchReader(__DIR__.'/../fixtures/data.csv', 5, 1, '|'); //0-4
        $this->assertTrue($batch->valid());
        $batch->seek(16);
        $batch->next();
        $this->assertFalse($batch->valid());
    }

    /**
     * @depends testValid
     */
    public function testBatch(CsvBatchReader $batch)
    {
        $batch->setHeaderRowNumber(2);
        $total = 0;

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(3, $count);
        $this->assertEquals(3, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(5, $count);
        $this->assertEquals(8, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(5, $count);
        $this->assertEquals(13, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(2, $count);
        $this->assertEquals(15, $total);
        $this->assertFalse($batch->valid());
        $this->assertFalse($batch->advance());
    }
}
