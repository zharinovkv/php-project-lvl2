<?php

namespace Differ\differ;

use function Differ\parsers\parser;

const ASSETS = 'assets/';

function genDiff($path_before, $path_after)
{
    $paths = getAbsolutePathToFile($path_before, $path_after);
    $content = parser($paths);

    [$before, $after] = [getBeforeOrAfter($content, 'path_before'), getBeforeOrAfter($content, 'path_after')];

    $repeatElements = toString(getElements(array_intersect($before, $after), ' '));
    $revisedElements = revisedElements($before, $after);
    $unicalElementsAfter = toString(getElements(array_diff_key($before, $after), '-'));
    $unicalElementsBefore = toString(getElements(array_diff_key($after, $before), '+'));

    return "{\n{$repeatElements}{$revisedElements}{$unicalElementsAfter}{$unicalElementsBefore}}\n";
}

function getAbsolutePathToFile(...$path)
{
    return [
        'path_before' => ! (bool)substr_count($path[0], '/') ? $path[0] = ASSETS . $path[0] : $path[0],
        'path_after' => ! (bool)substr_count($path[1], '/') ? $path[1] = ASSETS . $path[1] : $path[1]
    ];
}

function getBeforeOrAfter($content, $index)
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
