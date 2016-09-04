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

class CsvReaderTest extends TestCase
{
    public function testCurrent()
    {
        $reader = new CsvReader(__DIR__.'/../fixtures/data.csv', '|');
        $this->assertCount(34, $reader->current());

        return $reader;
    }

    /**
     * @depends testCurrent
     */
    public function testCount(CsvReader $reader)
    {
        $this->assertCount(17, $reader);
    }

    /**
     * @depends testCurrent
     */
    public function testNextKey(CsvReader $reader)
    {
        $this->assertEquals(0, $reader->key());
        $reader->next();
        $this->assertEquals(1, $reader->key());
        $reader->next();
        $this->assertEquals(2, $reader->key());
        $reader->next();
        $this->assertEquals(3, $reader->key());

        return $reader;
    }

    /**
     * @depends testNextKey
     */
    public function testSeek(CsvReader $reader)
    {
        $reader->seek(0);
        $this->assertEquals(0, $reader->key());
        $reader->seek(3);
        $this->assertEquals(3, $reader->key());

        return $reader;
    }

    /**
     * @depends testSeek
     */
    public function testValid(CsvReader $reader)
    {
        $this->assertTrue($reader->valid());
        $reader->seek($reader->count());
        $reader->next();
        $this->assertFalse($reader->valid());

        return $reader;
    }

    /**
     * @depends testValid
     */
    public function testRewind(CsvReader $reader)
    {
        $this->assertFalse($reader->valid());
        $reader->rewind();
        $this->assertTrue($reader->valid());
        $this->assertEquals(0, $reader->key());
    }

    /**
     * @depends testCurrent
     */
    public function testSerializeUnserialize(CsvReader $reader)
    {
        $this->assertCount(17, $reader);
        $serialized = serialize($reader);
        $this->assertInternalType('string', $serialized);

        $reader1 = unserialize($serialized);
        $this->assertInstanceOf(CsvReader::class, $reader1);
        $this->assertCount(17, $reader1);
    }

    /**
     * @depends testNextKey
     */
    public function testColumns(CsvReader $reader)
    {
        $reader->selectColumns(['ID' => 1, 'SpiffDescription' => 4, 'Payout' => 6]);
        $reader->seek(3);
        $row = $reader->current();

        $this->assertEquals(3, count($row));
        $this->assertEquals('2481440313', $row['ID']);
        $this->assertEquals('EDPC Monthly Spiff - CAP - Simple Choice Upgrade Spiff', $row['SpiffDescription']);
        $this->assertEquals('40.00000000000000', $row['Payout']);
    }

    /**
     * @depends testCurrent
     */
    public function testHeaderRowNumber(CsvReader $reader)
    {
        $reader->rewind();
        $this->assertEquals(0, $reader->key());
        $this->assertCount(17, $reader);

        $reader->setHeaderRowNumber(1);
        $reader->rewind();
        $this->assertEquals(1, $reader->key());
        $this->assertCount(16, $reader);

        return $reader;
    }

    /**
     * @depends testHeaderRowNumber
     */
    public function testIterator(CsvReader $reader)
    {
        $count = 0;
        foreach ($reader as $row) {
            ++$count;
        }
        $this->assertEquals(16, $count);
    }
}
