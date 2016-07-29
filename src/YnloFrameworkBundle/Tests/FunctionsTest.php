<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
                    'name' => 'Tom',
                ],
                'mother' => null,
            ],
            'boyfriend' => [
                'name' => 'Tomy',
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
