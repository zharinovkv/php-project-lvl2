<?php

namespace Differ\differ;

use Symfony\Component\Yaml\Yaml;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $content = selectContentFiles($path_before, $path_after);

    $beforeArr = $content['before'];
    $afterArr = $content['after'];

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
                    $result .= '+ ' . $key_after . ': ' . $value_after . PHP_EOL .
                        '- ' . $key_before . ': ' . $value_before . PHP_EOL;
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

    $newline = "\n";
    $str = "{{$newline}{$repeatElements}{$revisedElements}{$unicalElementsAfter}{$unicalElementsBefore}}";
    return $str;
}

function selectContentFiles($path_before, $path_after)
{
    $extention = pathinfo($path_before, PATHINFO_EXTENSION);
    $content = [];

    if ($extention == 'json') {
        $content['before'] = fileGetContents($path_before);
        $content['after'] = fileGetContents($path_after);
    } elseif ($extention == 'yaml') {
        $content['before'] = Yaml::parseFile($path_before);
        $content['after'] = Yaml::parseFile($path_after);
    } elseif ($extention == 'ini') {
        // parse = ini.parse;
    }
    return $content;
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
    $path = getPathToFile($path);
    $json = file_get_contents($path);
    return (array)json_decode($json);
}

function getPathToFile($path)
{
    return !(bool)substr_count($path, '/') ? $path = ASSETS . $path : $path;
}
