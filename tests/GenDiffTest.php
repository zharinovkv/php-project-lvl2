<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;


class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff_2()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff_3()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff_4()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff_5()
    {
        $expected = file_get_contents('./tests/fixtures/expected_bar.txt');
        $diff = genDiff('assets/before_bar.json', 'assets/after_bar.json');
        $this->assertNotEquals($expected, $diff);
    }
    
    public function testGenDiff_6()
    {
        $this->markTestIncomplete('Этот тест ещё не реализован.');

        $expected = file_get_contents('./tests/fixtures/expected_tree.txt');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json');
        //print_r($diff);
        $this->assertNotEquals($expected, $diff);
    }
}
