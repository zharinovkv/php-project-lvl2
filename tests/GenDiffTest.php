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
        $fixtures = "tests/fixtures/";

        $expected = file_get_contents("tests/expects/$expect");
        $diff = genDiff("{$fixtures}{$before}", "{$fixtures}{$after}", $format);
        $this->assertEquals($expected, $diff);
    }

    public function additionProvider()
    {
        return [
            "testGenDiffJson" => [
                "pretty.txt",
                "before.json",
                "after.json"
            ],
            "testGenDiffJsonPretty" => [
                "pretty.txt",
                "before.json",
                "after.json",
                "pretty"
            ],
            "testGenDiffJsonPlain" => [
                "plain.txt",
                "before.json",
                "after.json",
                "plain"
            ],
            "testGenDiffJsonJson" => [
                "json.txt",
                "before.json",
                "after.json",
                "json"
            ],
            "testGenDiffYaml" => [
                "pretty.txt",
                "before.yaml",
                "after.yaml"
            ],
            "testGenDiffYamlPretty" => [
                "pretty.txt",
                "before.json",
                "after.json",
                "pretty"
            ],
            "testGenDiffYamlPlain" => [
                "plain.txt",
                "before.json",
                "after.json",
                "plain"
            ],
            "testGenDiffYamlJson" => [
                "json.txt",
                "before.json",
                "after.json",
                "json"
            ]
        ];
    }
}
