<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;
use function Differ\differ\genDiff2;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff2()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff3()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff4()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff5()
    {
        $expected = file_get_contents('./tests/fixtures/expected_bar.txt');
        $diff = genDiff('assets/before_bar.json', 'assets/after_bar.json');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff6()
    {
        $expected = file_get_contents('./tests/fixtures/expected_tree.txt');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff7()
    {
        $expected = file_get_contents('./tests/fixtures/expected_plain.txt');
        $diff = genDiff2('assets/before_tree.json', 'assets/after_tree.json');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff8()
    {
        $this->markTestIncomplete('Этот тест ещё не реализован.');
        $expected = file_get_contents('./tests/fixtures/expected_json.txt');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json');
        $this->assertEquals($expected, $diff);
    }
    
}
