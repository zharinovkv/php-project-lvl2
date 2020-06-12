<?php

namespace Differ\gendifftest;

use PHPUnit\Framework\TestCase;

use function Differ\gendiff\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testGenDiff($expect, $before, $after, $format = "pretty")
    {
        $fixtures = "tests/fixtures/";
        $expected = file_get_contents("{$fixtures}{$expect}");
        $diff = genDiff("{$fixtures}{$before}", "{$fixtures}{$after}", $format);
        $this->assertEquals($expected, $diff);
    }

    public function additionProvider()
    {
        return [
            "testGenDiffJson" => [
                "expect_pretty.txt",
                "before.json",
                "after.json"
            ],
            "testGenDiffJsonPretty" => [
                "expect_pretty.txt",
                "before.json",
                "after.json",
                "pretty"
            ],
            "testGenDiffJsonPlain" => [
                "expect_plain.txt",
                "before.json",
                "after.json",
                "plain"
            ],
            "testGenDiffJsonJson" => [
                "expect_json.txt",
                "before.json",
                "after.json",
                "json"
            ],
            "testGenDiffYaml" => [
                "expect_pretty.txt",
                "before.yaml",
                "after.yaml"
            ],
            "testGenDiffYamlPretty" => [
                "expect_pretty.txt",
                "before.yaml",
                "after.yaml",
                "pretty"
            ],
            "testGenDiffYamlPlain" => [
                "expect_plain.txt",
                "before.yaml",
                "after.yaml",
                "plain"
            ],
            "testGenDiffYamlJson" => [
                "expect_json.txt",
                "before.yaml",
                "after.yaml",
                "json"
            ]
        ];
    }
}
