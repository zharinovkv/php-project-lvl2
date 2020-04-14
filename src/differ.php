<?php

namespace Differ\differ;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $content = \Differ\parser\parser($path_before, $path_after);

    $beforeArr = getContent($content, 'before');
    $afterArr = getContent($content, 'after');

    $repeatElements = toString(getElements(array_intersect($beforeArr, $afterArr), ' '));
    $revisedElements = revisedElements($beforeArr, $afterArr);
    $unicalElementsAfter = toString(getElements(array_diff_key($beforeArr, $afterArr), '-'));
    $unicalElementsBefore = toString(getElements(array_diff_key($afterArr, $beforeArr), '+'));

    return "{\n{$repeatElements}{$revisedElements}{$unicalElementsAfter}{$unicalElementsBefore}}\n";
}

function getContent($content, $index)
{
    return $content[$index];
}

function revisedElements($beforeArr, $afterArr)
{
    $result = '';

    foreach ($beforeArr as $key_before => $value_before) {
        foreach ($afterArr as $key_after => $value_after) {
            if ($key_before == $key_after && $value_before != $value_after) {
                $result .= '+ ' . $key_after . ': ' . $value_after . PHP_EOL . '- ' .
                    $key_before . ': ' . $value_before . PHP_EOL;
                break;
            }
        }
    }
    return $result;
}

function getElements($array, $prefix)
{
    $result = [];
    foreach ($array as $key => $value) {
        array_push($result, "{$prefix} {$key}: " . json_encode($value));
    }
    return $result;
}

function toString($array)
{
    $result = implode(PHP_EOL, $array);
    $result = str_replace("\"", "", $result);
    return $result . PHP_EOL;
}

function fileGetContents($path)
{
    $path = getPathToFile($path);
    $json = file_get_contents($path);
    return json_decode($json, true);
}

function getPathToFile($path)
{
    return !(bool)substr_count($path, '/') ? $path = ASSETS . $path : $path;
}
