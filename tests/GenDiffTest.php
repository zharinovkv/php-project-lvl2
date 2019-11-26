<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;
use function Differ\gendiff\genDiff;
use function Differ\parser\parser;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $this->assertEquals($expected, genDiff('assets/before.json', 'assets/after.json'));
    }

    public function testGenDiff2()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $this->assertNotEquals($expected, genDiff('assets/before.json', 'assets/after.json'));
    }

    public function testGenDiff3()
    {
        $expected = file_get_contents('./tests/fixtures/expected1.txt');
        $this->assertEquals($expected, parser('assets/before.yaml', 'assets/after.yaml'));
    }

    public function testGenDiff4()
    {
        $expected = file_get_contents('./tests/fixtures/expected2.txt');
        $this->assertNotEquals($expected, parser('assets/before.yaml', 'assets/after.yaml'));
    }
}
