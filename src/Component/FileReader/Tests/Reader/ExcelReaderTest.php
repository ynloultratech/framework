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
use YnloFramework\Component\FileReader\Reader\ExcelReader;

class ExcelReaderTest extends TestCase
{
    public function testCurrent()
    {
        $reader = new ExcelReader(__DIR__.'/../fixtures/data.xlsx');
        $this->assertCount(17, $reader->current());

        return $reader;
    }

    /**
     * @depends testCurrent
     */
    public function testCount(ExcelReader $reader)
    {
        $this->assertCount(1507, $reader);
    }

    /**
     * @depends testCurrent
     */
    public function testNextKey(ExcelReader $reader)
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
    public function testSeek(ExcelReader $reader)
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
    public function testValid(ExcelReader $reader)
    {
        $this->assertTrue($reader->valid());
        $reader->seek($reader->count() - 1);
        $reader->next();
        $this->assertFalse($reader->valid());

        return $reader;
    }

    /**
     * @depends testValid
     */
    public function testRewind(ExcelReader $reader)
    {
        $this->assertFalse($reader->valid());
        $reader->rewind();
        $this->assertTrue($reader->valid());
        $this->assertEquals(0, $reader->key());
    }

    /**
     * @depends testCurrent
     */
    public function testSerializeUnserialize(ExcelReader $reader)
    {
        $this->assertCount(1507, $reader);
        $serialized = serialize($reader);
        $this->assertInternalType('string', $serialized);

        $reader1 = unserialize($serialized);
        $this->assertInstanceOf(ExcelReader::class, $reader1);
        $this->assertCount(1507, $reader1);
    }

    /**
     * @depends testNextKey
     */
    public function testColumns(ExcelReader $reader)
    {
        $reader->selectColumns(['AccNum' => 1, 'Dist' => 4, 'Format' => 6]);
        $reader->seek(3);
        $row = $reader->current();

        $this->assertEquals(3, count($row));
        $this->assertEquals('CPD1012', $row['AccNum']);
        $this->assertEquals('CPD', $row['Dist']);
        $this->assertEquals('Argos', $row['Format']);
    }

    /**
     * @depends testCurrent
     */
    public function testHeaderRowNumber(ExcelReader $reader)
    {
        $reader->rewind();
        $this->assertEquals(0, $reader->key());
        $this->assertCount(1507, $reader);

        $reader->setHeaderRowNumber(1);
        $reader->rewind();
        $this->assertCount(1506, $reader);
        $this->assertEquals(1, $reader->key());

        return $reader;
    }

    /**
     * @depends testHeaderRowNumber
     */
    public function testIterator(ExcelReader $reader)
    {
        $count = 0;
        foreach ($reader as $row) {
            ++$count;
        }
        $this->assertEquals(1506, $count);
    }
}
