<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $content = \Differ\differ\genDiff('assets/before.json', 'assets/after.json');
        $this->assertEquals($expected, \Differ\differ\parser($content));
    }

    public function testGenDiff2()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $content = \Differ\differ\genDiff('assets/before.json', 'assets/after.json');
        $this->assertNotEquals($expected, \Differ\differ\parser($content));
    }

    public function testGenDiff3()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $content = \Differ\differ\genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertEquals($expected, \Differ\differ\parser($content));
    }

    public function testGenDiff4()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $content = \Differ\differ\genDiff('assets/before.yaml', 'assets/after.yaml');
        $this->assertNotEquals($expected, \Differ\differ\parser($content));
    }
}
