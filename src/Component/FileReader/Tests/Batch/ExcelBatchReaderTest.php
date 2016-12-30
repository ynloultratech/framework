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
use YnloFramework\Component\FileReader\Batch\ExcelBatchReader;

class ExcelBatchReaderTest extends TestCase
{
    public function testValid()
    {
        $batch = new ExcelBatchReader(__DIR__.'/../fixtures/data.xlsx', 500);//0-499
        $this->assertTrue($batch->valid());
        $this->assertCount(1507, $batch);

        return $batch;
    }

    public function testTruncateSeek()
    {
        $batch = new ExcelBatchReader(__DIR__.'/../fixtures/data.xlsx', 500); //0-499
        $this->assertEquals(0, $batch->key());
        $batch->seek(502);
        $this->assertEquals(499, $batch->key());
    }

    public function testNotAdvance()
    {
        $batch = new ExcelBatchReader(__DIR__.'/../fixtures/data.xlsx', 2000); ////0-1506
        $this->assertTrue($batch->valid());
        $batch->seek(1506);
        $batch->next();
        $this->assertFalse($batch->valid());
        $this->assertFalse($batch->advance());
    }

    public function testInvalid()
    {
        $batch = new ExcelBatchReader(__DIR__.'/../fixtures/data.xlsx', 2000); ////0-1506
        $this->assertTrue($batch->valid());
        $batch->seek(1506);
        $batch->next();
        $this->assertFalse($batch->valid());
    }

    /**
     * @depends testValid
     */
    public function testBatch(ExcelBatchReader $batch)
    {
        $batch->setHeaderRowNumber(2);
        $total = 0;

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(498, $count);
        $this->assertEquals(498, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(500, $count);
        $this->assertEquals(998, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(500, $count);
        $this->assertEquals(1498, $total);
        $this->assertFalse($batch->valid());
        $this->assertTrue($batch->advance());

        $count = 0;
        foreach ($batch as $row) {
            ++$count;
        }
        $total += $count;

        $this->assertEquals(7, $count);
        $this->assertEquals(1505, $total);
        $this->assertFalse($batch->valid());
        $this->assertFalse($batch->advance());
    }
}
