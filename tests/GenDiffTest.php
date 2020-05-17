<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff1()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff2()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_false.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff3()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff4()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_false.txt');
        $diff = genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff5()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true_bar.txt');
        $diff = genDiff('before_bar.json', 'after_bar.json');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff6()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_stacked.txt');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff7()
    {
        $expected = file_get_contents('./tests/fixtures/plain_stacked.txt');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json', 'plain');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff8()
    {
        $expected = file_get_contents('./tests/fixtures/json.json');
        $diff = genDiff('assets/before.json', 'assets/after.json', 'json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff9()
    {
        $expected = file_get_contents('./tests/fixtures/json_stacked.json');
        $diff = genDiff('assets/before_tree.json', 'assets/after_tree.json', 'json');
        $this->assertEquals($expected, $diff);
    }
}
