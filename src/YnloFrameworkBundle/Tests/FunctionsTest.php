<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\Tests;


class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayKeyValue()
    {
        $array = [
            'id' => 1,
            'name' => 'Angela',
            'parents' => [
                'father' => [
                    'name' => 'Tom'
                ],
                'mother' => null,
            ],
            'boyfriend' => [
                'name' => 'Tomy'
            ],
            'age' => 18,
        ];

        self::assertEquals('Angela', array_key_value($array, 'name'));
        self::assertEquals('Tom', array_key_value($array, 'parents.father.name'));
        self::assertEquals(null, array_key_value($array, 'parents.mother.name'));
        self::assertEquals('Tomy', array_key_value($array, 'boyfriend.name'));
        self::assertEquals(18, array_key_value($array, 'age'));
        self::assertEquals(20, array_key_value($array, 'boyfriend.age', 20));
        self::assertEquals(null, array_key_value($array, 'boyfriend.parents'));
    }
}
