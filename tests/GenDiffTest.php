<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiffJson()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff("./tests/fixtures/before.json", "./tests/fixtures/after.json");
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffJsonPPretty()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'pretty');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffJsonPlain()
    {
        $expected = file_get_contents('./tests/expects/plain.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'plain');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiffJsonJson()
    {
        $expected = file_get_contents('./tests/expects/json.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffYaml()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff('./tests/fixtures/before.yaml', './tests/fixtures/after.yaml');
        $this->assertEquals($expected, $diff);
    }
    
    public function testGenDiffYamlPretty()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'pretty');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffYamlPlain()
    {
        $expected = file_get_contents('./tests/expects/plain.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'plain');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffYamlJson()
    {
        $expected = file_get_contents('./tests/expects/json.txt');
        $diff = genDiff('./tests/fixtures/before.json', './tests/fixtures/after.json', 'json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffRelativePath()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff(__DIR__ . '/.././tests/fixtures/before.json', __DIR__ .  '/.././tests/fixtures/after.json');
        $this->assertEquals($expected, $diff);
    }

    public function testGenDiffAbsolutePath()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff(__DIR__ . '/fixtures/before.json', __DIR__ .  '/fixtures/after.json');
        $this->assertEquals($expected, $diff);
    }
}
