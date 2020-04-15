<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;
use function Differ\differ\getAbsolutePathToFile;
use const Differ\differ\ASSETS;

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
        $this->markTestIncomplete('Этот тест ещё не реализован.');
        $expected = __DIR__ . 'before.yaml';
        $path = getAbsolutePathToFile(['before', 'after']);
        $this->assertNotEquals($expected, $path);
    }

    public function testGetPathToFile2()
    {
        $this->markTestIncomplete('Этот тест ещё не реализован.');
        $expected = ASSETS . 'before.yaml';
        $path = getAbsolutePathToFile(['before', 'after']);
        $this->assertEquals($expected, $path);
        $this->markTestIncomplete(
            'Этот тест ещё не реализован.'
        );
    }

    public function testGetPathToFile3()
    {
        $this->markTestIncomplete('Этот тест ещё не реализован.');
        $expected = __DIR__ . 'assets/before.yaml';
        $path = getAbsolutePathToFile(['before', 'after']);
        $this->assertNotEquals($expected, $path);
        $this->markTestIncomplete(
            'Этот тест ещё не реализован.'
        );
    }
}
