<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;

use const Differ\settings\ASSETS;

class GenDiffTest extends TestCase
{
    public function testGenDiff1()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff(ASSETS . "before.json", ASSETS . "after.json");
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff10()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff('assets/before.json', 'assets/after.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff11()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff(__DIR__ . '/../assets/before.json', __DIR__ .  '/../assets/after.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff2()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_false.txt');
        $diff = genDiff(ASSETS . 'before.json', ASSETS . 'after.json');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff3()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true.txt');
        $diff = genDiff(ASSETS . 'before.yaml', ASSETS . 'after.yaml');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff4()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_false.txt');
        $diff = genDiff(ASSETS . 'before.yaml', ASSETS . 'after.yaml');
        $this->assertNotEquals($expected, $diff);
    }

    public function testGenDiff5()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_true_bar.txt');
        $diff = genDiff(ASSETS . 'before_bar.json', ASSETS . 'after_bar.json');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff6()
    {
        $expected = file_get_contents('./tests/fixtures/pretty_stacked.txt');
        $diff = genDiff(ASSETS . 'before_tree.json', ASSETS . 'after_tree.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff7()
    {
        $expected = file_get_contents('./tests/fixtures/plain_stacked.txt');
        $diff = genDiff(ASSETS . 'before_tree.json', ASSETS . 'after_tree.json', 'plain');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiff8()
    {
        $expected = file_get_contents('./tests/fixtures/json.json');
        $diff = genDiff(ASSETS . 'before.json', ASSETS . 'after.json', 'json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiff9()
    {
        $expected = file_get_contents('./tests/fixtures/json_stacked.json');
        $diff = genDiff(ASSETS . 'before_tree.json', ASSETS . 'after_tree.json', 'json');
        $this->assertEquals($expected, $diff);
    }
}
