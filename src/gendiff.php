<?php

namespace Differ\gendiff;

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

    $result = Docopt::handle($doc, array('version' => '0.0.1'));
    $diff = genDiff($result->args["<firstFile>"], $result->args["<secondFile>"]);
    echo $diff;
}

function genDiff($path_before, $path_after)
{
    $beforeArr = fileGetContents($path_before);
    $afterArr = fileGetContents($path_after);
    
        $repeatElements = function () use ($beforeArr, $afterArr) {
            $result = array_intersect($beforeArr, $afterArr);
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

        $revisedElements = function () use ($beforeArr, $afterArr) {

            $result = '';

            foreach ($beforeArr as $key_before => $value_before) {
                foreach ($afterArr as $key_after => $value_after) {
                    if ($key_before == $key_after && $value_before != $value_after) {
                        $result .= '+ ' . $key_after . ': ' . $value_after . PHP_EOL . '- ' . $key_before . ': ' . $value_before . PHP_EOL;
                        break;
                    }
                }
            }

            return $result;
        };
    
        $repeatElements = toString(getElements($repeatElements, ' '));
        $revisedElements = $revisedElements();
        $unicalElementsAfter = toString(getElements($unicalElementsAfter, '-'));
        $unicalElementsBefore = toString(getElements($unicalElementsBefore, '+'));

        $str = '{' . PHP_EOL . $repeatElements . $revisedElements . $unicalElementsAfter .
            $unicalElementsBefore . '}';
        return $str;
}


function getElements($func, $prefix)
{
    $result = [];
    $arr = $func();
    foreach ($arr as $key => $value) {
        array_push($result, "{$prefix} {$key}: " . json_encode($value));
    }
    return $result;
}

function toString($array)
{
    $result = implode(PHP_EOL, $array) . PHP_EOL;
    $result = str_replace("\"", "", $result);
    return $result;
}

function fileGetContents($path)
{
    $json = file_get_contents($path);
    return (array) json_decode($json);
}