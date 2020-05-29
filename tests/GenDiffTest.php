<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff1()
    {
        $expected = file_get_contents('./tests/expects/pretty.txt');
        $diff = genDiff("./tests/fixtures/before.json", "./tests/fixtures/after.json");
        $this->assertEquals($expected, $diff);
    }
}
