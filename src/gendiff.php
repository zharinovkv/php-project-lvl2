<?php

namespace Differ\gendiff;

use function Funct\Collection\union;

function genDiff($path_before, $path_after)
{

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
   
    $result = union(
        $unUnicalElements,
        $revisedElementsBefor,
        $revisedElementsAfter,
        $unicalElementsBefore,
        $unicalElementsAfter
    );

    $str = toString($result);
    $str = str_replace('"', '', $str);
    echo $str;
    return $str;
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
