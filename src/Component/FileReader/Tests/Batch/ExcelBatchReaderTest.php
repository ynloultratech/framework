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
        $batch = new ExcelBatchReader(__DIR__.'/../fixtures/data.xlsx', 500);
        $this->assertTrue($batch->valid());
        $this->assertCount(1507, $batch);

        return $batch;
    }

    /**
     * @depends testValid
     * @expectedException \InvalidArgumentException
     */
    public function testBatchLength(ExcelBatchReader $batch)
    {
        $this->assertEquals(500, $batch->getBatchLength());
        $batch->setBatchLength(1000);
        $this->assertEquals(1000, $batch->getBatchLength());
        $this->assertCount(1507, $batch);

        $batch->setBatchLength(2000);
        // avoid batch length greater than total
        $this->assertEquals(1507, $batch->getBatchLength());
        $this->assertEquals(0, $batch->key());

        $batch->seek(1506);
        $batch->next();
        $this->assertFalse($batch->valid());

        // The batch length must be an integer positive.
        // Expected exception InvalidArgumentException
        $batch->setBatchLength(-23);
    }

    /**
     * @depends testValid
     */
    public function testBatch(ExcelBatchReader $batch)
    {
        $batch->setBatchLength(500);
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
