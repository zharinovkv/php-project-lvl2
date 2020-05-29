<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expect, $before, $after, $format = "pretty")
    {
        $expected = file_get_contents($expect);
        $diff = genDiff($before, $after, $format);
        $this->assertEquals($expected, $diff);
    }

    public function additionProvider()
    {
        return [
            "testGenDiffJson" => [
                "./tests/expects/pretty.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json"
            ],
            "testGenDiffJsonPretty" => [
                "./tests/expects/pretty.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "pretty"
            ],
            "testGenDiffJsonPlain" => [
                "./tests/expects/plain.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "plain"
            ],
            "testGenDiffJsonJson" => [
                "./tests/expects/json.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "json"
            ],
            "testGenDiffYaml" => [
                "./tests/expects/pretty.txt",
                "./tests/fixtures/before.yaml",
                "./tests/fixtures/after.yaml"
            ],
            "testGenDiffYamlPretty" => [
                "./tests/expects/pretty.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "pretty"
            ],
            "testGenDiffYamlPlain" => [
                "./tests/expects/plain.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "plain"
            ],
            "testGenDiffYamlJson" => [
                "./tests/expects/json.txt",
                "./tests/fixtures/before.json",
                "./tests/fixtures/after.json",
                "json"
            ],
            "testGenDiffRelativePath" => [
                "./tests/expects/pretty.txt",
                "../php-project-lvl2/tests/fixtures/before.json",
                "../php-project-lvl2/tests/fixtures/after.json"
            ],
            "testGenDiffAbsolutePath" => [
                "./tests/expects/pretty.txt",
                __DIR__ . "/fixtures/before.json",
                __DIR__ . "/fixtures/after.json"
            ]
        ];
    }
}
