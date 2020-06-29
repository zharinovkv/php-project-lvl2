<?php

namespace Differ\Test;

use PHPUnit\Framework\TestCase;

use function Differ\gendiff\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionProviderFormats
     */
    public function testGenDiff($expect, $before, $after, $format = "pretty")
    {
        $fixtures = "tests/fixtures/";
        $expected = file_get_contents("{$fixtures}{$expect}");
        $diff = genDiff("{$fixtures}{$before}", "{$fixtures}{$after}", $format);
        $this->assertEquals($expected, $diff);
    }

    /**
     * @dataProvider additionProviderPaths
     */
    public function testGenDiffPaths($path, $format = "pretty")
    {
        $expected = file_get_contents("tests/fixtures/expect_pretty.txt");
        $diff = genDiff("{$path}before.json", "{$path}after.json", $format);
        $this->assertEquals($expected, $diff);
    }

    public function additionProviderPaths()
    {
        return [
            "relative" => [
                __DIR__ . "/../tests/fixtures/"
            ],
            "relative2" => [
                "tests/fixtures/"
            ],
            "relative3" => [
                "../php-project-lvl2/tests/fixtures/"
            ],
            "absolute" => [
                __DIR__ . "/fixtures/"
            ]
        ];
    }

    public function additionProviderFormats()
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
