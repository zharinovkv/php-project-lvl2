<?php

namespace php_project_lvl2\gendiff;

use Docopt;

function run()
{
    $doc = <<<'DOCOPT'
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

  Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]

DOCOPT;

    $result = Docopt::handle($doc, array('version' => '1.0.0rc2'));

    $path_before = $result->args["<firstFile>"];
    $path_after = $result->args["<secondFile>"];

    $before = file_get_contents($path_before);
    $beforeArr = (array) json_decode($before);

    $after = file_get_contents($path_after);
    $afterArr = (array) json_decode($after);

    $repeatElements = function () use ($beforeArr, $afterArr) {
        $result = array_intersect($beforeArr, $afterArr);
        return $result;
    };

    $revisedElementsBefor = function () use ($beforeArr, $afterArr) {
        $result = array_diff(
            array_intersect_key($afterArr, $beforeArr),
            array_intersect_key($beforeArr, $afterArr)
        );
        return $result;
    };
    
    $revisedElementsAfter = function () use ($beforeArr, $afterArr) {
        $result = array_diff(
            array_intersect_key($beforeArr, $afterArr),
            array_intersect_key($afterArr, $beforeArr)
        );
        return $result;
    };

    $unicalElementsBefore = function () use ($beforeArr, $afterArr) {
        $result = array_diff_key($afterArr, $beforeArr);
        return $result;
    };
    
    $unicalElementsAfter = function () use ($beforeArr, $afterArr) {
        $result = array_diff_key($beforeArr, $afterArr);
        return $result;
    };

    $unUnicalElements = arToAr($repeatElements, ' ');
    $revisedElementsBefor = arToAr($revisedElementsBefor, '+');
    $revisedElementsAfter = arToAr($revisedElementsAfter, '-');
    $unicalElementsBefore = arToAr($unicalElementsBefore, '+');
    $unicalElementsAfter = arToAr($unicalElementsAfter, '-');

    $result = array_merge($unUnicalElements, $revisedElementsBefor, $revisedElementsAfter, $unicalElementsBefore, $unicalElementsAfter);
    $str = toString($result);
    $str = str_replace('"', '', $str);
    echo $str;
}

function arToAr($func, $prefix)
{
    $result = [];
    $arr = $func();
    foreach ($arr as $key => $value) {
        array_push($result, "{$prefix} {$key}: " . json_encode($value));
    }
    return $result;
}

function toString($result)
{
    $str = '{' . PHP_EOL . implode(PHP_EOL, $result) . PHP_EOL . '}' . PHP_EOL;
    return $str;
}
