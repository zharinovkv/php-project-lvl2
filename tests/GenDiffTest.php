<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;
use function Differ\differ\getPathToFile;
use const Differ\differ\ASSETS;

//use * Differ\differ;


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

    public function testGetPathToFile()
    {
        $expected = __DIR__ . 'before.yaml';
        $path = getPathToFile('before.yaml');
        $this->assertNotEquals($expected, $path);
    }

    public function testGetPathToFile2()
    {
        $expected = ASSETS . 'before.yaml';
        $path = getPathToFile('before.yaml');
        $this->assertEquals($expected, $path);
    }

    public function testGetPathToFile3()
    {
        $expected = __DIR__ . 'assets/before.yaml';
        $path = getPathToFile('before.yaml');
        $this->assertNotEquals($expected, $path);
    }
}
