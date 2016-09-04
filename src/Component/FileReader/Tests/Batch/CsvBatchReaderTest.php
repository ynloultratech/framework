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
        $batch = new CsvBatchReader(__DIR__.'/../fixtures/data.csv', 3, '|');
        $this->assertTrue($batch->valid());
        $this->assertCount(17, $batch);

        return $batch;
    }

    /**
     * @depends testValid
     * @expectedException \InvalidArgumentException
     */
    public function testBatchLength(CsvBatchReader $batch)
    {
        $this->assertEquals(3, $batch->getBatchLength());
        $batch->setBatchLength(5);
        $this->assertEquals(5, $batch->getBatchLength());
        $this->assertCount(17, $batch);

        $batch->setBatchLength(22);
        // avoid batch length greater than total
        $this->assertEquals(17, $batch->getBatchLength());
        $this->assertEquals(0, $batch->key());

        $batch->seek(17);
        $batch->next();
        $this->assertFalse($batch->advance());

        // The batch length must be an integer positive.
        // Expected exception InvalidArgumentException
        $batch->setBatchLength(-23);
    }

    /**
     * @depends testValid
     */
    public function testBatch(CsvBatchReader $batch)
    {
        $batch->setBatchLength(5);
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
